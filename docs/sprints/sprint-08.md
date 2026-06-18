# Sprint 8 — Conta de Comprador e Engajamento Social

**Objetivo:** Introduzir o tipo de usuário "comprador" — sem perfil de negócio, sem dashboard
de produtor. Sobre essa base, implementar favoritos e avaliações de produtos. Ao fim desta
sprint, um visitante pode criar uma conta simples, salvar produtos favoritos e deixar uma
avaliação com nota e comentário.

**Esta sprint representa a maior mudança arquitetural do projeto:** a premissa de que
"todo usuário é um produtor" (RN-02) é reformulada com a introdução de um campo `role`.
Tudo que depende de usuário autenticado precisa ser revisto para distinguir os dois papéis.

**Duração estimada:** dois dias

---

## Backlog

---

### BUY-01 — Conta de comprador (sistema de roles)

**Descrição:**
Adicionar um campo `role` à tabela `users` com dois valores possíveis: `producer` e `buyer`.
Criar um fluxo de cadastro separado para compradores em `/register/comprador`.
Compradores passam pela verificação de e-mail, mas não são direcionados ao setup de perfil.
Produtores mantêm seu fluxo atual intacto.

**Migration:**
```php
$table->enum('role', ['producer', 'buyer'])->default('producer')->after('email');
```

**Regras de negócio:**
- Todo usuário criado via `/register` existente continua sendo `producer` (padrão)
- Usuários criados via `/register/comprador` têm `role = buyer`
- O middleware `EnsureProducerProfileComplete` deve verificar se o usuário é `producer`
  antes de redirecionar para o setup (compradores não passam pelo setup)
- Um comprador não pode acessar o dashboard de produtor
- Um produtor não pode usar funcionalidades de comprador (favoritos, avaliações)

**Novos helpers no Model `User`:**
```php
public function isProducer(): bool { return $this->role === 'producer'; }
public function isBuyer(): bool    { return $this->role === 'buyer'; }
```

**Novas rotas:**
```php
// Cadastro de comprador (sem setup de perfil)
Route::get('/register/comprador', [BuyerRegisterController::class, 'create'])
    ->name('buyer.register');
Route::post('/register/comprador', [BuyerRegisterController::class, 'store'])
    ->name('buyer.register.store');
```

**`BuyerRegisterController`:**
- Cria usuário com `role = 'buyer'`
- Campos: nome, e-mail, senha (igual ao cadastro de produtor)
- Redireciona para verificação de e-mail após o cadastro

**Atualizar `EnsureProducerProfileComplete`:**
```php
// Só redireciona para setup se o usuário for producer
if ($request->user()->isProducer() && ! $request->user()->producer) {
    return redirect()->route('producer.setup');
}
```

**Navegação:**
- Link "Criar conta como comprador" na tela de login e no registro atual de produtor
- O header distingue compradores de produtores no dropdown de perfil:
  - Produtor: Dashboard, Editar perfil, Sair
  - Comprador: Meus favoritos, Sair

**Critérios de aceitação:**
- [ ] Migration com `role` executando sem erro (default `producer` preserva usuários existentes)
- [ ] `/register/comprador` cria usuário com `role = buyer`
- [ ] Compradores verificam e-mail e são direcionados para a home (não para setup)
- [ ] Compradores não conseguem acessar `/dashboard` (recebem 403 ou redirecionamento)
- [ ] Produtores não são afetados pelo novo fluxo
- [ ] `isProducer()` e `isBuyer()` corretos no Model `User`
- [ ] Middleware de setup checando `isProducer()` antes de redirecionar
- [ ] Dropdown de perfil com opções distintas por role
- [ ] Link cruzado entre os dois formulários de cadastro

**Esforço:** M
**Dependências:** nenhuma

---

### BUY-02 — Favoritos

**Descrição:**
Compradores autenticados podem adicionar e remover produtos da sua lista de favoritos.
O botão de favorito aparece nos cards de produto e na página de detalhe. A lista de
favoritos é acessível em `/minha-conta/favoritos`.

**Migration:**
```php
Schema::create('favorites', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
    $table->unique(['user_id', 'product_id']);
});
```

**Relacionamentos no Model `User`:**
```php
public function favorites(): BelongsToMany
{
    return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
}
```

**Novas rotas:**
```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/favoritos/{product}',   [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favoritos/{product}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::get('/minha-conta/favoritos',  [FavoriteController::class, 'index'])->name('favorites.index');
});
```

**`FavoriteController`:**
- `store`: verifica se o usuário é `buyer`, adiciona à pivot (ignora se já existe)
- `destroy`: remove da pivot
- `index`: lista os produtos favoritados pelo usuário autenticado, com paginação

**Botão de favorito:**
- Visível apenas para compradores autenticados
- Coração ♡ (vazio) / ♥ (preenchido) dependendo do estado
- Na página de detalhe: botão com texto "Salvar nos favoritos" / "Salvo"
- Nos cards: ícone discreto no canto superior direito da foto
- Implementado via form `POST` / `DELETE` (SSR, sem JS assíncrono)

**Política de acesso:**
- Apenas `buyer` pode favoritar
- Comprador vê apenas seus próprios favoritos

**Critérios de aceitação:**
- [ ] Migration `favorites` executando sem erro
- [ ] `User::favorites()` retorna a coleção de produtos favoritados
- [ ] Toggle de favorito funciona (adiciona se não existe, remove se existe)
- [ ] Tentativa de favoritar por produtor ou visitante retorna 403
- [ ] Página `/minha-conta/favoritos` lista favoritos com paginação
- [ ] Botão de favorito reflete estado correto (salvo / não salvo) na página de detalhe
- [ ] Ícone nos cards visível apenas para compradores autenticados
- [ ] Exclusão do produto remove favorito por cascade

**Esforço:** M
**Dependências:** BUY-01

---

### BUY-03 — Avaliações de produtos

**Descrição:**
Compradores autenticados podem deixar uma avaliação (nota de 1 a 5 estrelas + comentário
opcional) em qualquer produto disponível. Cada comprador pode deixar apenas uma avaliação
por produto, podendo editá-la ou excluí-la depois. As avaliações aparecem na página de
detalhe do produto e a nota média aparece nos cards do catálogo.

**Migration:**
```php
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->unsignedTinyInteger('rating');  // 1 a 5
    $table->text('comment')->nullable();
    $table->timestamps();
    $table->unique(['user_id', 'product_id']);
});
```

**Model `Review`:**
```php
protected $fillable = ['user_id', 'product_id', 'rating', 'comment'];

public function user(): BelongsTo    { return $this->belongsTo(User::class); }
public function product(): BelongsTo { return $this->belongsTo(Product::class); }
```

**Relacionamento no Model `Product`:**
```php
public function reviews(): HasMany
{
    return $this->hasMany(Review::class);
}
```

**Novas rotas:**
```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/produtos/{product}/avaliacoes',         [ReviewController::class, 'store'])
        ->name('reviews.store');
    Route::delete('/produtos/{product}/avaliacoes/{review}', [ReviewController::class, 'destroy'])
        ->name('reviews.destroy');
});
```

**`ReviewController`:**
- `store`: valida `rating` (1-5, required) e `comment` (nullable, max 1000 chars);
  verifica que o usuário é `buyer`; verifica que o produtor do produto não é o próprio usuário;
  usa `updateOrCreate` para permitir edição da avaliação existente
- `destroy`: verifica que a avaliação pertence ao usuário autenticado

**Regras de validação:**
```php
'rating'  => ['required', 'integer', 'min:1', 'max:5'],
'comment' => ['nullable', 'string', 'max:1000'],
```

**Exibição na página de detalhe do produto (`products/show.blade.php`):**
- Seção "Avaliações" abaixo das informações do produto
- Nota média e total de avaliações: "⭐ 4.2 (17 avaliações)"
- Lista de avaliações: avatar placeholder + nome + estrelas + data + comentário
- Formulário de avaliação: visível apenas para compradores autenticados
  - Se comprador já avaliou: exibe a avaliação existente com botão "Excluir avaliação"
- Mensagem para visitantes: "Faça login como comprador para deixar sua avaliação"

**Exibição nos cards e catálogo:**
- Nota média (formato "⭐ 4.2") no `product-card__body` quando `reviews_count > 0`
- Eager load eficiente: `withAvg('reviews', 'rating')->withCount('reviews')`

**Critérios de aceitação:**
- [ ] Migration `reviews` executando sem erro
- [ ] Comprador pode criar, visualizar e excluir sua própria avaliação
- [ ] `updateOrCreate` permite editar avaliação existente reenviando o form
- [ ] Produtor não pode avaliar seus próprios produtos (403)
- [ ] Visitantes e produtores não veem o formulário de avaliação
- [ ] Nota média e total corretos na página de detalhe
- [ ] Nota média nos cards do catálogo (quando existir)
- [ ] Exclusão do usuário remove avaliações por cascade
- [ ] Validação de `rating` rejeita valores fora de 1-5
- [ ] Form com 5 estrelas interativas (inputs radio estilizados como estrelas)

**Esforço:** M
**Dependências:** BUY-01
