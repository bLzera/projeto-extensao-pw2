# Sprint 9 — Dashboard Avançado (possível)

**Objetivo:** Enriquecer o dashboard do produtor com informações e ferramentas de gestão
mais sofisticadas: métricas de visualização, controle de estoque, preços promocionais,
formulário de contato para visitantes e um painel administrativo básico para moderação.

Esta sprint é marcada como **possível** — depende de validação de necessidade após
a entrega das Sprints 7 e 8. Nenhum item aqui é pré-requisito para funcionalidades
já planejadas.

**Duração estimada:** dois a três dias

---

## Backlog

---

### DASH-01 — Métricas de visualização de produtos

**Descrição:**
Registrar quantas vezes cada produto foi visualizado na página de detalhe pública.
O produtor vê essas métricas no dashboard: total histórico e visualizações da última semana.

**Migration:**
```php
Schema::create('product_views', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->string('ip_hash', 64);  // SHA-256 do IP, nunca o IP bruto
    $table->timestamp('viewed_at');
    $table->index(['product_id', 'viewed_at']);
});
```

**Registro de view no `ProductController@show` (público):**
```php
ProductView::create([
    'product_id' => $product->id,
    'ip_hash'    => hash('sha256', $request->ip()),
    'viewed_at'  => now(),
]);
```

Sem deduplicação rígida por sessão no MVP — a mesma pessoa pode contar múltiplas vezes
em visitas distintas. O `ip_hash` permite análise futura sem armazenar dado pessoal bruto.

**No `DashboardController@index`:**
```php
$viewsThisWeek = $producer->products()
    ->join('product_views', 'products.id', '=', 'product_views.product_id')
    ->where('product_views.viewed_at', '>=', now()->subWeek())
    ->count();
```

**Exibição no dashboard:**
- Novo stat card: "X visualizações esta semana"
- Na tabela de produtos: nova coluna "Views (7 dias)"

**Critérios de aceitação:**
- [ ] Migration `product_views` executando sem erro
- [ ] View registrada a cada acesso à página de detalhe do produto
- [ ] `ip_hash` armazena hash SHA-256, não o IP bruto
- [ ] Stat card com total de visualizações da semana no dashboard
- [ ] Coluna de views na tabela de produtos do dashboard
- [ ] Exclusão do produto remove views por cascade

**Esforço:** M
**Dependências:** nenhuma

---

### DASH-02 — Controle de estoque quantitativo

**Descrição:**
Adicionar um campo opcional de quantidade em estoque ao produto. Quando preenchido,
o produtor informa a quantidade disponível; o produto é automaticamente marcado como
indisponível quando o estoque chega a zero. O catálogo exibe um aviso "Últimas X unidades"
quando o estoque é baixo.

**Migration:**
```php
$table->unsignedInteger('stock_quantity')->nullable()->after('is_available');
```

**Regras de negócio:**
- Campo `stock_quantity` é opcional (null = sem controle de estoque)
- Quando `stock_quantity` é definido e chega a 0: `is_available` é automaticamente
  definido como `false` (via accessor/observer ou verificação no controller)
- Limiar de "estoque baixo": `stock_quantity <= 5`

**Observer `ProductObserver` (ou método no Model):**
```php
protected static function booted(): void
{
    static::saving(function (Product $product) {
        if (! is_null($product->stock_quantity) && $product->stock_quantity <= 0) {
            $product->is_available = false;
        }
    });
}
```

**No formulário de produto (dashboard):**
- Campo numérico opcional: "Quantidade em estoque (deixe em branco para não controlar)"
- Exibir estoque atual na tabela do dashboard

**No catálogo público:**
- Badge "Últimas {{ $product->stock_quantity }} unidades" quando `stock_quantity <= 5`
- Não exibir o número exato quando `stock_quantity > 5` (evitar oversharing)

**Critérios de aceitação:**
- [ ] Migration executando sem erro
- [ ] Formulário de produto com campo de estoque opcional
- [ ] Produto automaticamente desativado quando `stock_quantity` chega a 0
- [ ] Badge de "Últimas X unidades" no catálogo quando estoque ≤ 5
- [ ] Coluna de estoque na tabela do dashboard
- [ ] Produtos sem `stock_quantity` definido não são afetados

**Esforço:** M
**Dependências:** nenhuma

---

### DASH-03 — Preço promocional

**Descrição:**
Permitir que o produtor defina um preço promocional para um produto. Quando definido,
o preço original aparece riscado e o preço promocional em destaque, com o percentual
de desconto calculado automaticamente.

**Migration:**
```php
$table->decimal('promo_price', 10, 2)->nullable()->after('price');
```

**Regra de negócio:** `promo_price` deve ser menor que `price`. Validação no Form Request.

**Accessor no Model `Product`:**
```php
public function getDiscountPercentAttribute(): ?int
{
    if (! $this->promo_price || $this->promo_price >= $this->price) return null;
    return (int) round((1 - $this->promo_price / $this->price) * 100);
}

public function getEffectivePriceAttribute(): float
{
    return $this->promo_price ?? $this->price;
}
```

**No formulário de produto:**
- Campo opcional: "Preço promocional (deixe em branco para não ter promoção)"
- Validação: `nullable|numeric|lt:price`

**No catálogo e cards:**
- Exibir `promo_price` em destaque com `$price` riscado e badge "−{X}%"
- Cards usam `effective_price` para exibição e ordenação

**Critérios de aceitação:**
- [ ] Migration executando sem erro
- [ ] Formulário com campo de preço promocional e validação `lt:price`
- [ ] Preço riscado + preço promocional + badge de desconto no catálogo
- [ ] Card de produto exibindo o preço correto (`effective_price`)
- [ ] Ordenação por "menor preço" usa `effective_price` (ou `COALESCE(promo_price, price)`)
- [ ] Produtos sem promoção não são afetados

**Esforço:** M
**Dependências:** CAT-EX-02 (para garantir que a ordenação por preço use o campo correto)

---

### DASH-04 — Formulário de contato por e-mail

**Descrição:**
Qualquer visitante (autenticado ou não) pode enviar uma mensagem direta ao produtor
pelo perfil público. O formulário coleta nome, e-mail e mensagem, e envia um e-mail
para `producer->contact_email`. Se este campo estiver vazio, o formulário não é exibido.

**Rota:**
```php
Route::post('/produtores/{producer}/contato', [ProducerContactController::class, 'send'])
    ->name('producers.contact')
    ->middleware('throttle:5,1'); // máximo 5 envios por minuto por IP
```

**`ProducerContactController@send`:**
- Valida: `sender_name` (required, max 100), `sender_email` (required, email),
  `message` (required, min 10, max 2000)
- Envia e-mail via Laravel Mail para `producer->contact_email`
- Redireciona de volta com flash de sucesso

**Mailable `ProducerContactMail`:**
```php
// Remetente: noreply@feiradingital.local
// Destinatário: producer->contact_email
// Reply-To: sender_email
// Subject: "Nova mensagem via Feira Digital — {producer->farm_name}"
```

**No perfil público do produtor (`producers/show.blade.php`):**
- Formulário colapsável (pode ser um `<details>` ou seção sempre visível)
- Visível apenas se `producer->contact_email` está preenchido
- Mensagem de confirmação após envio

**Limitação anti-spam:**
- Rate limiting via middleware `throttle:5,1` (5 requisições por minuto)
- Honeypot: campo hidden `website` que não deve ser preenchido; se preenchido, retorna 200
  sem enviar o e-mail (bot trap)

**Critérios de aceitação:**
- [ ] Formulário exibido apenas quando `contact_email` está preenchido
- [ ] E-mail enviado com `Reply-To` do remetente
- [ ] Rate limiting funcional (6ª tentativa retorna 429)
- [ ] Honeypot implementado
- [ ] Flash de sucesso após envio
- [ ] Formulário funciona para visitantes não autenticados

**Esforço:** M
**Dependências:** nenhuma

---

### DASH-05 — Painel administrativo básico

**Descrição:**
Criar um painel administrativo mínimo para moderação de conteúdo. O admin pode visualizar
todos os produtores e produtos cadastrados, e pode aplicar soft delete em registros
impróprios. A conta admin é criada via seeder, sem fluxo de cadastro público.

**Pré-requisitos no modelo:**
- Adicionar valor `admin` ao enum `role` da tabela `users` (migration adicional)
- Adicionar `SoftDeletes` nos models `Producer` e `Product`
- Migrations de soft delete: `$table->softDeletes()` em ambas as tabelas

**Criação da conta admin:**
```php
// AdminSeeder
User::create([
    'name'              => 'Administrador',
    'email'             => 'admin@feiradidital.local',
    'password'          => Hash::make(env('ADMIN_PASSWORD', 'changeme')),
    'role'              => 'admin',
    'email_verified_at' => now(),
]);
```

**Novas rotas (grupo `/admin`):**
```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',            [AdminController::class, 'index'])->name('index');
    Route::get('/produtores',  [AdminController::class, 'producers'])->name('producers');
    Route::get('/produtos',    [AdminController::class, 'products'])->name('products');
    Route::delete('/produtores/{producer}', [AdminController::class, 'deleteProducer'])->name('producers.delete');
    Route::delete('/produtos/{product}',    [AdminController::class, 'deleteProduct'])->name('products.delete');
});
```

**Middleware `admin`:**
```php
if (! $request->user()?->role === 'admin') abort(403);
```

**Views do painel:**
- `/admin` — resumo: total de produtores, total de produtos, total de usuários
- `/admin/produtores` — tabela paginada com nome, e-mail, cidade, data de cadastro, botão de exclusão
- `/admin/produtos` — tabela paginada com nome, produtor, categoria, preço, botão de exclusão

**Soft delete:** registros excluídos pelo admin não são permanentemente removidos.
Um futura sprint pode adicionar restauração.

**Critérios de aceitação:**
- [ ] `SoftDeletes` em `Producer` e `Product`
- [ ] Migrations de soft delete executando sem erro
- [ ] `AdminSeeder` cria conta admin (idempotente via `firstOrCreate`)
- [ ] Middleware `admin` bloqueia acesso a não-admins com 403
- [ ] Painel `/admin` com estatísticas gerais
- [ ] Listagem de produtores e produtos com paginação
- [ ] Soft delete funcional: registro não aparece no catálogo após exclusão
- [ ] Catálogo e perfil público filtram registros soft-deleted automaticamente (Eloquent faz isso por padrão)

**Esforço:** M
**Dependências:** BUY-01 (para o enum de role)
