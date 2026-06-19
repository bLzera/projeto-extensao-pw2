# Sprint 10 — Avaliações: Visibilidade e Curadoria

**Objetivo:** Tornar as avaliações visíveis e gerenciáveis. O comprador ganha controle sobre
a própria avaliação (editar, excluir, recriar). O produtor ganha um feed curado no perfil
público e um painel privado para gerenciar a visibilidade das avaliações recebidas. A nota
média permanece calculada pelo sistema, imune a qualquer curadoria manual.

**Duração estimada:** dois dias

---

## Modelo de dados

Uma migration adiciona três campos à tabela `ratings` existente antes dos demais itens:

| Campo | Tipo | Default | Propósito |
|---|---|---|---|
| `hidden` | `boolean` | `false` | Vendedor oculta avaliação do feed público |
| `status` | `enum('active','deleted')` | `'active'` | Soft delete pelo comprador |
| `edited_at` | `timestamp` | `null` | Sinaliza que a avaliação foi editada após criação |

**Regra central:** a nota média é sempre calculada sobre `status = 'active'`. O campo `hidden`
não afeta o cálculo — apenas controla a exibição no feed. O comprador não sabe que a sua
avaliação está oculta.

---

## Backlog

---

### RAT-01 — Migration de campos de ciclo de vida

**Descrição:**
Adicionar `hidden`, `status` e `edited_at` à tabela `ratings`.

**Migration:**
```php
Schema::table('ratings', function (Blueprint $table) {
    $table->boolean('hidden')->default(false)->after('comment');
    $table->enum('status', ['active', 'deleted'])->default('active')->after('hidden');
    $table->timestamp('edited_at')->nullable()->after('status');
});
```

**Atualizar `$fillable` no Model `Rating`:**
```php
protected $fillable = ['buyer_id', 'producer_id', 'stars', 'comment', 'hidden', 'status', 'edited_at'];
```

**Adicionar relacionamento filtrado no Model `Producer`:**
```php
public function activeRatings(): HasMany
{
    return $this->ratings()->where('status', 'active');
}
```

**Atualizar cálculo de média em `ProducerController`:**
As queries de `avg`, `count` e `withAvg` passam a usar `activeRatings` para ignorar
registros soft-deleted.

```php
// Em ProducerController@index
->withAvg('activeRatings', 'stars')

// Em ProducerController@show
$averageRating = $producer->activeRatings()->avg('stars');
$ratingsCount  = $producer->activeRatings()->count();
```

**Critérios de aceitação:**
- [ ] Migration executando sem erro
- [ ] Registros existentes herdam `hidden = false`, `status = 'active'`, `edited_at = null`
- [ ] Média calculada ignorando registros com `status = 'deleted'`

**Esforço:** P
**Dependências:** nenhuma

---

### RAT-02 — Seção "Avaliar / Sua avaliação" no perfil público

**Descrição:**
Atualizar a seção de avaliação na view `producers/show.blade.php`. O comportamento depende
do estado da avaliação do comprador autenticado:

- Comprador sem avaliação ativa → exibe o formulário de avaliação (comportamento atual)
- Comprador com avaliação ativa → exibe a própria avaliação com botões de editar e excluir

Esta seção aparece acima do feed de avaliações (RAT-03) e é sempre visível para o próprio
comprador, independente do campo `hidden` — o comprador não sabe que foi ocultado.

**Dados necessários em `ProducerController@show`:**
```php
$existingRating = Auth::check() && Auth::user()->isBuyer()
    ? $producer->ratings()
        ->where('buyer_id', Auth::id())
        ->where('status', 'active')
        ->first()
    : null;
```

**Ações disponíveis quando o comprador já avaliou:**
- "Editar" — exibe o formulário pré-preenchido com os valores atuais
- "Excluir" — form com `@method('DELETE')` apontando para `ratings.destroy` (ver RAT-05)

**Critérios de aceitação:**
- [ ] Comprador sem avaliação ativa vê o formulário de criar
- [ ] Comprador com avaliação ativa vê a própria avaliação com botões de editar e excluir
- [ ] Avaliação oculta pelo vendedor ainda aparece para o próprio comprador nesta seção
- [ ] Visitantes não autenticados e produtores não veem esta seção

**Esforço:** M
**Dependências:** RAT-01

---

### RAT-03 — Feed de avaliações no perfil público do produtor

**Descrição:**
Nova seção no perfil público (`producers/show.blade.php`), abaixo da seção de avaliação
do comprador. Exibe as 5 avaliações mais recentes com `status = 'active'` e `hidden = false`.
O contador no header conta todas as avaliações ativas (inclusive ocultas), sinalizando
transparência sem expor o conteúdo curado.

**Query:**
```php
$feedRatings = $producer->ratings()
    ->where('status', 'active')
    ->where('hidden', false)
    ->with('buyer')
    ->latest()
    ->take(5)
    ->get();
```

**Header do feed:**
- Total de avaliações ativas: "X avaliações" (baseado em `activeRatings()->count()`)
- Nota média atual

**Card de avaliação:**
- Ícone/avatar placeholder + nome do comprador
- Estrelas (1–5)
- Comentário (se houver; avaliações sem comentário exibem só a nota e o nome)
- Badge "editada" quando `edited_at` não é nulo

**Rodapé do feed:**
- Link "Ver todas as avaliações" → `producers.ratings.index` (RAT-07)
- Link aparece apenas quando há mais de 5 avaliações ativas visíveis

**Critérios de aceitação:**
- [ ] Feed exibe no máximo 5 avaliações, ordenadas da mais recente
- [ ] Avaliações com `hidden = true` não aparecem no feed
- [ ] Avaliações sem comentário aparecem (só estrelas + nome)
- [ ] Badge "editada" visível quando `edited_at` não é nulo
- [ ] Contador do header reflete o total de ativas (incluindo ocultas)
- [ ] Link "Ver todas" aparece apenas quando existem mais de 5 avaliações ativas visíveis

**Esforço:** M
**Dependências:** RAT-01

---

### RAT-04 — Editar avaliação (comprador)

**Descrição:**
O comprador pode editar a própria avaliação ativa pelo formulário pré-preenchido na seção
"Sua avaliação". O `RatingController::upsert` é refatorado para distinguir criação, edição
e reativação, definindo `edited_at` apenas em edições de avaliações ativas.

**`RatingController::upsert` refatorado:**
```php
public function upsert(Request $request, Producer $producer): RedirectResponse
{
    abort_if(! $request->user()->isBuyer(), 403);

    $request->validate([
        'stars'   => ['required', 'integer', 'min:1', 'max:5'],
        'comment' => ['nullable', 'string', 'max:1000'],
    ]);

    $existing = Rating::where('buyer_id', $request->user()->id)
        ->where('producer_id', $producer->id)
        ->first();

    if ($existing && $existing->status === 'active') {
        $existing->update([
            'stars'     => $request->stars,
            'comment'   => $request->comment,
            'edited_at' => now(),
        ]);
    } elseif ($existing && $existing->status === 'deleted') {
        // Reativação — tratada como nova avaliação (ver RAT-06)
        $existing->update([
            'stars'     => $request->stars,
            'comment'   => $request->comment,
            'status'    => 'active',
            'hidden'    => false,
            'edited_at' => null,
        ]);
    } else {
        Rating::create([
            'buyer_id'    => $request->user()->id,
            'producer_id' => $producer->id,
            'stars'       => $request->stars,
            'comment'     => $request->comment,
        ]);
    }

    return redirect()
        ->route('producers.show', $producer)
        ->with('success', 'Avaliação enviada com sucesso!');
}
```

**Regras:**
- `hidden` não é alterado durante a edição — a curadoria do vendedor persiste
- `edited_at` é definido apenas na edição de avaliação ativa, não na reativação

**Critérios de aceitação:**
- [ ] Reenviar o formulário com avaliação ativa atualiza `stars`, `comment` e `edited_at`
- [ ] `hidden` permanece intacto após edição
- [ ] Badge "editada" aparece no feed após a edição
- [ ] Primeira criação não define `edited_at`

**Esforço:** P
**Dependências:** RAT-01, RAT-02

---

### RAT-05 — Excluir avaliação com soft delete (comprador)

**Descrição:**
O comprador pode excluir a própria avaliação ativa. A exclusão é soft delete: define
`status = 'deleted'`, preservando o registro no banco. A nota some da média do produtor.
A seção da avaliação retorna ao formulário de criar nova avaliação.

**Nova rota:**
```php
Route::delete('/produtores/{producer}/avaliar', [RatingController::class, 'destroy'])
    ->name('ratings.destroy')
    ->middleware(['auth', 'verified']);
```

**`RatingController::destroy`:**
```php
public function destroy(Request $request, Producer $producer): RedirectResponse
{
    abort_if(! $request->user()->isBuyer(), 403);

    $rating = Rating::where('buyer_id', $request->user()->id)
        ->where('producer_id', $producer->id)
        ->where('status', 'active')
        ->firstOrFail();

    $rating->update(['status' => 'deleted']);

    return redirect()
        ->route('producers.show', $producer)
        ->with('success', 'Avaliação removida.');
}
```

**Na view:** botão "Excluir" usa form com `@method('DELETE')`. Exibir confirmação via
`onclick="return confirm('Tem certeza que deseja remover sua avaliação?')"` para evitar
exclusão acidental.

**Critérios de aceitação:**
- [ ] Avaliação excluída desaparece da seção "Sua avaliação" e do feed público
- [ ] Nota do registro excluído some da média do produtor
- [ ] O registro permanece no banco com `status = 'deleted'`
- [ ] Comprador vê o formulário de nova avaliação após excluir
- [ ] Tentativa de excluir avaliação de outro usuário retorna 404

**Esforço:** P
**Dependências:** RAT-01, RAT-02

---

### RAT-06 — Re-avaliação após exclusão

**Descrição:**
Quando um comprador que excluiu a própria avaliação tenta criar uma nova, o sistema
reativa o registro existente com `status = 'deleted'`, tratando a operação como nova
avaliação. Do ponto de vista do comprador, é indistinguível de uma primeira avaliação.

Esta lógica está embutida no `RatingController::upsert` refatorado em RAT-04
(bloco `elseif ($existing && $existing->status === 'deleted')`).

**Campos resetados na reativação:**
- `status = 'active'`
- `hidden = false` (nasce visível, como toda avaliação nova)
- `edited_at = null` (não é uma edição, é uma nova avaliação)

**Critérios de aceitação:**
- [ ] Comprador que excluiu pode criar nova avaliação normalmente
- [ ] Avaliação reativada começa com `hidden = false`
- [ ] Avaliação reativada não tem badge "editada"
- [ ] Apenas um registro por par `(buyer_id, producer_id)` existe no banco em qualquer estado

**Esforço:** P (coberto pela implementação de RAT-04)
**Dependências:** RAT-05

---

### RAT-07 — Página de avaliações: `/produtores/{producer}/avaliacoes`

**Descrição:**
Página dedicada com todas as avaliações visíveis do produtor, paginadas (10 por página).
Acessada pelo link "Ver todas as avaliações" no rodapé do feed. Usa o mesmo card de
avaliação do feed do perfil público.

**Nova rota:**
```php
Route::get('/produtores/{producer}/avaliacoes', [ProducerController::class, 'ratings'])
    ->name('producers.ratings.index');
```

**`ProducerController::ratings`:**
```php
public function ratings(Producer $producer)
{
    $ratings = $producer->ratings()
        ->where('status', 'active')
        ->where('hidden', false)
        ->with('buyer')
        ->latest()
        ->paginate(10);

    return view('producers.ratings', compact('producer', 'ratings'));
}
```

**View `producers/ratings.blade.php`:**
- Header: nome do produtor + total de avaliações visíveis
- Lista paginada de cards (mesmo componente do feed)
- Link de volta ao perfil do produtor

**Critérios de aceitação:**
- [ ] Rota acessível publicamente (sem autenticação)
- [ ] Apenas avaliações `status = active AND hidden = false` aparecem
- [ ] Paginação de 10 por página com links de navegação
- [ ] Contador no header reflete o total de avaliações visíveis

**Esforço:** P
**Dependências:** RAT-03

---

### RAT-08 — Painel privado do vendedor: gerenciar avaliações

**Descrição:**
Nova seção no dashboard do produtor listando todas as avaliações recebidas com
`status = 'active'` (ocultas e visíveis). O produtor pode alternar a visibilidade
de cada avaliação individualmente via toggle. Não pode editar nem excluir avaliações.

**Nova rota:**
```php
Route::patch('/dashboard/avaliacoes/{rating}/visibilidade', [DashboardRatingController::class, 'toggle'])
    ->name('dashboard.ratings.toggle')
    ->middleware(['auth', 'verified']);
```

**`DashboardRatingController::toggle`:**
```php
public function toggle(Request $request, Rating $rating): RedirectResponse
{
    abort_if($rating->producer->user_id !== $request->user()->id, 403);

    $rating->update(['hidden' => ! $rating->hidden]);

    return back()->with('success', $rating->hidden ? 'Avaliação ocultada.' : 'Avaliação exibida.');
}
```

**View — seção no dashboard:**
- Header: total de avaliações ativas, média atual, quantidade de avaliações ocultas
- Tabela paginada (10 por página) com:
  - Nome do comprador
  - Estrelas
  - Comentário (truncado em linhas longas)
  - Badge "editada" quando aplicável
  - Indicador de estado: "Visível" / "Oculta"
  - Botão toggle: "Ocultar" / "Exibir" (form com PATCH)

**Critérios de aceitação:**
- [ ] Seção acessível apenas pelo próprio produtor autenticado
- [ ] Avaliações com `status = 'deleted'` não aparecem na lista
- [ ] Toggle altera `hidden` e reflete na view imediatamente (redirect back)
- [ ] Tentativa de toggle em avaliação de outro produtor retorna 403
- [ ] Avaliação ocultada some do feed público e da página de avaliações
- [ ] Header com estatísticas (total, média, ocultas) correto

**Esforço:** M
**Dependências:** RAT-01

---

## Fora do escopo desta sprint

- Sistema de requisição de moderação e painel admin (depende de DASH-05, Sprint 9)
- Notificações ao vendedor sobre nova avaliação (sprint dedicada futura)
- O comprador saber que sua avaliação foi ocultada (decisão intencional de design)
