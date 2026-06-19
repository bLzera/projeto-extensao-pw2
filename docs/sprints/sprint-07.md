# Sprint 7 — Catálogo e Descoberta

**Objetivo:** Ampliar as formas de descoberta de produtos e produtores, tornar as URLs
amigáveis para SEO, e adicionar atalhos de contato direto. Ao fim desta sprint, o catálogo
permite filtro por cidade, ordenação, busca de produtores, e os produtos têm URLs com
slug. Esta sprint herda o design system refinado da Sprint 6.

**Duração estimada:** um dia

---

## Backlog

---

### CAT-EX-01 — Filtro por cidade no catálogo

**Descrição:**
Adicionar um segundo eixo de filtragem no catálogo público: a cidade do produtor.
O filtro é implementado via query string `?cidade=lontras` e é combinável com
`?categoria=` e `?busca=` já existentes. As cidades disponíveis são derivadas
dinamicamente dos produtores que têm pelo menos um produto disponível.

**Mudanças no `HomeController@index`:**
```php
->when($request->cidade, fn($q, $city) =>
    $q->whereHas('producer', fn($q) => $q->where('city', $city))
)
```

**Coletar cidades disponíveis:**
```php
$cities = Producer::whereHas('products', fn($q) => $q->where('is_available', true))
    ->distinct()
    ->orderBy('city')
    ->pluck('city');
```

**Mudanças na view `home/index.blade.php`:**
- Segunda row de filtros abaixo do `category-nav`, com as cidades como pills
- Estilo idêntico ao `category-nav` (reutilizar a classe)
- Exibir apenas se houver mais de uma cidade disponível
- Refletir a cidade ativa com `category-nav__item--active`

**Mudanças no flash de "Filtrando por:":**
- Incluir a cidade selecionada quando presente

**Critérios de aceitação:**
- [x] Pills de cidade exibidas abaixo das categorias (só quando houver ≥ 2 cidades)
- [x] `?cidade=` filtra produtos pelo campo `city` do produtor
- [x] Filtros de cidade, categoria e busca funcionam combinados
- [x] `withQueryString()` preserva `?cidade=` nos links de paginação
- [x] Cidade selecionada refletida no bloco de "Filtrando por:"
- [x] Link "Limpar filtros" remove os três parâmetros

**Esforço:** P
**Dependências:** nenhuma

---

### CAT-EX-02 — Ordenação configurável no catálogo

**Descrição:**
Adicionar uma opção de ordenação ao catálogo via query string `?ordem=`. O padrão
é `recentes` (comportamento atual com `latest()`). A ordenação persiste com os demais
filtros ativos e é apresentada como um `<select>` no topo direito do catálogo.

**Valores aceitos para `?ordem=`:**

| Valor         | Comportamento Eloquent               |
|---------------|--------------------------------------|
| `recentes`    | `->latest()` (padrão)                |
| `menor-preco` | `->orderBy('price', 'asc')`          |
| `maior-preco` | `->orderBy('price', 'desc')`         |
| `az`          | `->orderBy('name', 'asc')`           |

**Mudança no `HomeController@index`:**
```php
->when($request->ordem, function ($q, $ordem) {
    match ($ordem) {
        'menor-preco' => $q->orderBy('price', 'asc'),
        'maior-preco' => $q->orderBy('price', 'desc'),
        'az'          => $q->orderBy('name', 'asc'),
        default       => $q->latest(),
    };
}, fn($q) => $q->latest())
```

**View:** dropdown `<select>` com `onchange="this.form.submit()"` dentro do form de busca
ou em um form próprio que preserva os demais query params como `<input type="hidden">`.

**Critérios de aceitação:**
- [x] Dropdown de ordenação visível no catálogo (acima ou no mesmo nível da busca)
- [x] Quatro opções de ordenação funcionando corretamente
- [x] Ordenação selecionada refletida no `<select>` ao navegar
- [x] `withQueryString()` preserva `?ordem=` na paginação
- [x] Combinável com `?categoria=`, `?busca=` e `?cidade=`

**Esforço:** P
**Dependências:** CAT-EX-01

---

### CAT-EX-03 — Busca de produtores

**Descrição:**
A página `/produtores` atualmente lista todos os produtores sem nenhum filtro.
Adicionar um campo de busca por nome do negócio (`farm_name`) e cidade (`city`),
via query string `?busca=`, com contagem de resultados e empty state contextual.

**Mudança no `ProducerController@index`:**
```php
public function index(Request $request)
{
    $producers = Producer::withCount(['products' => fn($q) => $q->where('is_available', true)])
        ->when($request->busca, fn($q, $term) =>
            $q->where(function ($q) use ($term) {
                $q->where('farm_name', 'like', "%{$term}%")
                  ->orWhere('city', 'like', "%{$term}%");
            })
        )
        ->orderBy('farm_name')
        ->paginate(12)
        ->withQueryString();

    return view('producers.index', [
        'producers' => $producers,
        'busca'     => $request->busca,
    ]);
}
```

**Mudança na view `producers/index.blade.php`:**
- Adicionar o campo de busca com o mesmo estilo `search-bar` do catálogo
- Exibir contagem: "X produtores encontrados" (ou "X produtores" sem busca)
- Empty state com ícone 🏡 quando busca não retorna resultados

**Critérios de aceitação:**
- [x] Campo de busca na página de produtores
- [x] Busca por nome do negócio e por cidade (OR)
- [x] Contagem de resultados exibida
- [x] Paginação preserva `?busca=`
- [x] Empty state contextual quando busca retorna vazio

**Esforço:** P
**Dependências:** nenhuma

---

### CAT-EX-04 — Slugs SEO nas URLs

**Descrição:**
Atualmente as URLs de produto e produtor usam o ID numérico (`/produtos/3`).
A troca para slugs amigáveis (`/produtos/tomate-organico`) melhora legibilidade
e SEO. A geração do slug é automática ao criar/editar, com verificação de unicidade.

**Migrations:**
```php
// add_slug_to_products_table
$table->string('slug')->nullable()->unique()->after('name');

// add_slug_to_producers_table
$table->string('slug')->nullable()->unique()->after('farm_name');
```

**Geração automática de slug nos Models:**

Em `Product`:
```php
protected static function booted(): void
{
    static::saving(function (Product $product) {
        if (empty($product->slug)) {
            $product->slug = static::generateSlug($product->name);
        }
    });
}

protected static function generateSlug(string $name): string
{
    $base = Str::slug($name);
    $slug = $base;
    $i    = 1;

    while (static::where('slug', $slug)->exists()) {
        $slug = "{$base}-{$i}";
        $i++;
    }

    return $slug;
}
```

Aplicar a mesma lógica em `Producer`, baseada em `farm_name`.

**Atualizar rotas:**
```php
Route::get('/produtos/{product:slug}', ...)->name('products.show');
Route::get('/produtores/{producer:slug}', ...)->name('producers.show');
Route::get('/produtores/{producer:slug}/...', ...); // demais rotas de produtor
```

**Migrar slugs de registros existentes:**
Criar um `artisan` command ou incluir no seeder/migration um loop que gera slug
para registros sem slug (para o DemoSeeder existente).

**Critérios de aceitação:**
- [x] Migrations criadas e executando sem erro
- [x] Slug gerado automaticamente ao criar produto ou produtor
- [x] Slug gerado automaticamente ao criar produtor no setup
- [x] Slugs únicos: sufixo `-2`, `-3`, etc. quando há colisão
- [x] Rotas usando `{product:slug}` e `{producer:slug}`
- [x] Links em todo o sistema usando `route('products.show', $product)` (Eloquent route model binding resolve automaticamente)
- [x] Registros do DemoSeeder têm slugs válidos
- [x] IDs antigos (`/produtos/3`) retornam 404 (comportamento esperado do route binding)

**Esforço:** M
**Dependências:** nenhuma

---

### CAT-EX-05 — Botão WhatsApp formatado

**Descrição:**
O campo `whatsapp` já existe no modelo `Producer`, mas nenhuma view formata o número
como um link clicável. O botão deve gerar uma URL `wa.me/{numero}` com uma mensagem
pré-preenchida contextual ao produto ou ao produtor.

**Formato do link:**
```
https://wa.me/55{numero_limpo}?text={mensagem_urlencoded}
```
Onde `numero_limpo` remove todos os caracteres não numéricos do campo `whatsapp`.

**Mensagem padrão (perfil do produtor):**
```
Olá! Encontrei sua loja na Feira Digital e gostaria de mais informações.
```

**Mensagem contextual (página do produto):**
```
Olá! Vi o produto "{nome_do_produto}" na Feira Digital e tenho interesse. Pode me dar mais informações?
```

**Implementar como método no Model `Producer`:**
```php
public function whatsappUrl(?string $productName = null): ?string
{
    if (! $this->whatsapp) return null;

    $number  = preg_replace('/\D/', '', $this->whatsapp);
    $message = $productName
        ? "Olá! Vi o produto \"{$productName}\" na Feira Digital e tenho interesse."
        : "Olá! Encontrei sua loja na Feira Digital e gostaria de mais informações.";

    return "https://wa.me/55{$number}?text=" . urlencode($message);
}
```

**Views que recebem o botão:**
- `products/show.blade.php` — botão proeminente no bloco de contato
- `producers/show.blade.php` — botão no bloco de contato

**Estilo:** botão verde com ícone de WhatsApp (SVG inline simples ou emoji 💬).
Deve ser um `<a>` com `target="_blank" rel="noopener"`.

**Critérios de aceitação:**
- [x] Método `whatsappUrl()` no Model `Producer`
- [x] Botão exibido apenas quando `producer->whatsapp` está preenchido
- [x] Link abre WhatsApp Web/App com mensagem pré-preenchida
- [x] Mensagem contextual ao produto na página do produto
- [x] Número formatado sem espaços/traços/parênteses
- [x] `target="_blank" rel="noopener"` no link

**Esforço:** P
**Dependências:** nenhuma

---

### CAT-EX-06 — Produtos em destaque

**Descrição:**
Permitir que o produtor marque até 3 produtos como destaque. No perfil público do produtor,
os produtos em destaque aparecem primeiro. No dashboard, o produtor pode ativar/desativar
o destaque de cada produto, com um aviso quando o limite de 3 é atingido.

**Migration:**
```php
$table->boolean('is_featured')->default(false)->after('is_available');
```

**Regra de negócio:** um produtor não pode ter mais de 3 produtos `is_featured = true`
ao mesmo tempo. A verificação ocorre no controller antes de ativar o destaque.

**Nova rota:**
```php
Route::patch('/dashboard/produtos/{product}/destaque', [ProductController::class, 'toggleFeatured'])
    ->name('producer.products.toggleFeatured');
```

**`ProductController@toggleFeatured`:**
```php
public function toggleFeatured(Product $product)
{
    $this->authorize('update', $product);

    $producer = auth()->user()->producer;

    if (! $product->is_featured) {
        $featuredCount = $producer->products()->where('is_featured', true)->count();
        if ($featuredCount >= 3) {
            return redirect()->route('dashboard')
                ->with('error', 'Você já tem 3 produtos em destaque. Remova um antes de adicionar outro.');
        }
    }

    $product->update(['is_featured' => ! $product->is_featured]);

    return redirect()->route('dashboard');
}
```

**Mudanças no perfil público do produtor (`producers/show.blade.php`):**
- Produtos ordenados: `->orderByDesc('is_featured')->orderBy('name')`
- Produtos em destaque recebem um badge "⭐ Destaque" no card

**Mudanças no dashboard:**
- Nova coluna "Destaque" na tabela de produtos
- Botão "Destacar" / "Remover destaque" por produto
- Flash de erro quando limite de 3 é atingido

**Critérios de aceitação:**
- [x] Migration com `is_featured` executando sem erro
- [x] Produtor pode ativar destaque de até 3 produtos
- [x] Tentativa de adicionar 4º destaque retorna mensagem de erro clara
- [x] Produtos em destaque aparecem primeiro no perfil público
- [x] Badge de destaque visível no perfil público
- [x] Coluna e botão de destaque presentes na tabela do dashboard
- [x] `ProductPolicy` cobre o novo método `toggleFeatured`

**Esforço:** P
**Dependências:** nenhuma
