# Sprint 4 — Catálogo Público

**Objetivo:** Visitante consegue navegar o catálogo completo sem autenticação — filtrar por
categoria, buscar por nome, ver o detalhe de um produto e o perfil completo de um produtor.

**Duração estimada:** meio dia

---

## Backlog

---

### CAT-01 — HomeController (catálogo paginado com filtro e busca)

**Descrição:**
Criar `HomeController@index` que monta a query de produtos disponíveis (`is_available = true`)
com suporte a filtro por categoria (query string `?categoria=slug`) e busca por nome
(`?busca=termo`). Os dois parâmetros podem ser combinados simultaneamente.

**Query Eloquent base:**
```php
Product::with(['producer', 'category'])
    ->where('is_available', true)
    ->when($request->categoria, fn($q, $slug) =>
        $q->whereHas('category', fn($q) => $q->where('slug', $slug))
    )
    ->when($request->busca, fn($q, $term) =>
        $q->where('name', 'like', "%{$term}%")
    )
    ->latest()
    ->paginate(12)
    ->withQueryString(); // preserva filtros nos links de paginação
```

**Dados passados à view:**
- `$products` — coleção paginada
- `$categories` — todas as categorias (para o menu de filtro)
- `$currentCategory` — slug da categoria ativa (ou null)
- `$busca` — termo de busca atual (para manter o campo preenchido)

**Critérios de aceitação:**
- [ ] Rota `GET /` mapeada para `HomeController@index`
- [ ] Listagem exibe apenas produtos disponíveis
- [ ] Filtro por categoria funciona via query string
- [ ] Busca por nome funciona via query string (case-insensitive)
- [ ] Filtro e busca funcionam combinados
- [ ] `withQueryString()` preserva parâmetros nos links de paginação
- [ ] Eager loading (`with`) evita N+1 queries

**Esforço:** P
**Dependências:** AUTH-01

---

### CAT-02 — View do Catálogo (Home)

**Descrição:**
Criar `resources/views/home/index.blade.php`. Layout: barra de busca no topo, menu horizontal
de categorias (todos + cada categoria com indicador visual de selecionada), grid de cards
de produto, links de paginação no rodapé.

**Componente reutilizável:** `resources/views/components/product-card.blade.php`
- Exibe: foto do produto (com fallback para imagem placeholder), nome, produtor, preço + unidade, badge de categoria
- Link para `GET /produtos/{product}`

**Estado vazio:** se nenhum produto for encontrado, exibir mensagem amigável.

**Critérios de aceitação:**
- [ ] View criada com grid responsivo de cards
- [ ] Menu de categorias com estado ativo na categoria selecionada
- [ ] Campo de busca pré-preenchido com o termo atual
- [ ] Componente `product-card` funcional e reutilizável
- [ ] Paginação exibida corretamente
- [ ] Estado vazio com mensagem adequada
- [ ] Link "Limpar filtros" quando há filtro ou busca ativa

**Esforço:** M
**Dependências:** CAT-01

---

### CAT-03 — Página de Detalhe do Produto

**Descrição:**
Criar `ProductController@show` e a view `products/show.blade.php`. Deve exibir todos os
detalhes do produto e um bloco de "Contato do produtor" com os dados de contato disponíveis
(telefone, WhatsApp, e-mail), com links clicáveis (`tel:`, `https://wa.me/`, `mailto:`).

**Critérios de aceitação:**
- [ ] Rota `GET /produtos/{product}` com route model binding
- [ ] Produto indisponível retorna 404 (ou `abort(404)` se `is_available = false`)
- [ ] Foto do produto exibida em tamanho maior
- [ ] Todos os campos do produto exibidos (nome, descrição, preço, unidade, categoria)
- [ ] Bloco de contato exibe apenas os campos preenchidos pelo produtor
- [ ] Link "← Voltar ao catálogo" funcional
- [ ] Link para o perfil completo do produtor

**Esforço:** P
**Dependências:** CAT-02

---

### CAT-04 — Listagem Pública de Produtores

**Descrição:**
Criar `ProducerController@index` e a view `producers/index.blade.php`. Listagem paginada
de todos os produtores cadastrados. Componente `producer-card` com: foto, nome do negócio,
cidade e link para o perfil.

**Critérios de aceitação:**
- [ ] Rota `GET /produtores` mapeada
- [ ] Listagem paginada (12 por página)
- [ ] Componente `producer-card` criado e reutilizável
- [ ] Link "Produtores" no header navegando para esta rota

**Esforço:** P
**Dependências:** AUTH-01

---

### CAT-05 — Página de Perfil Público do Produtor

**Descrição:**
Criar `ProducerController@show` e a view `producers/show.blade.php`. Exibe os dados completos
do perfil do produtor e a listagem de seus produtos disponíveis (paginada, 8 por página).

**Critérios de aceitação:**
- [ ] Rota `GET /produtores/{producer}` com route model binding
- [ ] Dados do produtor: foto, nome do negócio, descrição, cidade
- [ ] Bloco de contato com os canais disponíveis (tel:, wa.me, mailto:)
- [ ] Grid de produtos do produtor (somente disponíveis)
- [ ] Paginação na listagem de produtos do produtor
- [ ] Estado vazio se produtor não tiver produtos disponíveis

**Esforço:** P
**Dependências:** CAT-04
