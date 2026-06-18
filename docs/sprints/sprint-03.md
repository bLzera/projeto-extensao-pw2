# Sprint 3 — Gestão de Produtos

**Objetivo:** Produtor autenticado consegue criar, editar, remover e alternar a disponibilidade
de seus produtos pelo dashboard. Upload de imagem de produto funcionando.

**Duração estimada:** meio dia

---

## Backlog

---

### PROD-01 — ProductPolicy (autorização)

**Descrição:**
Criar `ProductPolicy` para garantir que um produtor só consiga editar, atualizar e deletar
produtos que pertencem ao seu próprio perfil de produtor. Registrar a policy no
`AuthServiceProvider` (ou via descoberta automática do Laravel 11).

**Métodos da policy:**
- `update(User $user, Product $product)`: `$user->producer->id === $product->producer_id`
- `delete(User $user, Product $product)`: mesma lógica

**Critérios de aceitação:**
- [x] `ProductPolicy` criada em `app/Policies/`
- [x] Policy registrada e funcional
- [x] Tentativa de editar produto de outro produtor retorna 403

**Esforço:** P
**Dependências:** AUTH-01

---

### PROD-02 — Form Request de Produto

**Descrição:**
Criar `StoreProductRequest` e `UpdateProductRequest` com as regras de validação para criação
e edição de produto. A distinção entre os dois é que no update a foto é opcional.

**Regras comuns:**
- `name`: required, string, max:255
- `description`: nullable, string, max:2000
- `price`: required, numeric, min:0.01, decimal:0,2
- `unit`: required, string, max:50
- `category_id`: required, exists:categories,id
- `is_available`: boolean (checkbox, nullable = false)

**Regras específicas:**
- `StoreProductRequest` → `photo`: required, image, max:2048, mimes:jpg,jpeg,png,webp
- `UpdateProductRequest` → `photo`: nullable, image, max:2048, mimes:jpg,jpeg,png,webp

**Critérios de aceitação:**
- [x] Dois Form Requests criados em `app/Http/Requests/`
- [x] Validação de `price` aceita vírgula e ponto como separador decimal (cast manual)
- [x] `category_id` validado contra registros existentes

**Esforço:** P
**Dependências:** AUTH-01

---

### PROD-03 — Producer\ProductController (CRUD)

**Descrição:**
Criar `Producer\ProductController` com os seguintes métodos. Todas as rotas ficam sob o
prefixo `/dashboard/produtos` e protegidas pelos três middlewares (auth, verified, profile).

**Métodos:**
- `create()` — exibe formulário de novo produto com lista de categorias
- `store(StoreProductRequest)` — salva produto, faz upload da foto, redireciona ao dashboard
- `edit(Product)` — exibe formulário pré-preenchido (via route model binding)
- `update(UpdateProductRequest, Product)` — atualiza produto, troca foto se enviada
- `destroy(Product)` — remove produto e sua foto do storage
- `toggleAvailability(Product)` — alterna `is_available` e retorna ao dashboard

**Critérios de aceitação:**
- [x] CRUD completo funcional
- [x] Route model binding funcionando em `edit`, `update`, `destroy`, `toggleAvailability`
- [x] Policy `ProductPolicy` aplicada em `update`, `destroy` (via `$this->authorize()`)
- [x] Upload de foto salvo em `storage/app/public/products/` com nome único (`Str::uuid()`)
- [x] Foto anterior removida ao atualizar com nova imagem
- [x] Foto removida ao deletar produto
- [x] Redirecionamentos com flash messages (sucesso/erro)

**Esforço:** M
**Dependências:** PROD-01, PROD-02

---

### PROD-04 — View do Formulário de Produto (compartilhada)

**Descrição:**
Criar `resources/views/dashboard/products/form.blade.php` que serve tanto para criação quanto
para edição. A distinção entre os modos é feita via variável `$product` (null = criação,
objeto = edição) e `$action` (URL do POST/PUT) passadas pelo controller.

**Campos do formulário:**
- Nome do produto (text)
- Descrição (textarea)
- Preço (number, step 0.01) + Unidade (select: kg, g, unidade, dúzia, caixa, litro, mL)
- Categoria (select populado via `$categories`)
- Foto (file; no modo edição, exibir foto atual se existir)
- Disponível (checkbox)

**Critérios de aceitação:**
- [x] View única reutilizada em `create` e `edit`
- [x] Campos pré-preenchidos no modo edição
- [x] Foto atual exibida no modo edição (se existir)
- [x] Erros de validação exibidos por campo
- [x] Select de unidade com opções fixas razoáveis
- [x] Select de categoria populado dinamicamente

**Esforço:** M
**Dependências:** PROD-03

---

### PROD-05 — Listagem de Produtos no Dashboard

**Descrição:**
Atualizar `Producer\DashboardController@index` para passar os produtos do produtor logado
à view, paginados (10 por página). Atualizar a view `dashboard/index.blade.php` para exibir
a tabela de produtos com: foto (thumbnail), nome, categoria, preço, unidade, status
(disponível/indisponível) e ações (editar, toggle disponibilidade, excluir).

**Critérios de aceitação:**
- [x] Listagem exibe apenas produtos do produtor autenticado
- [x] Paginação server-side com 10 itens por página
- [x] Botão de toggle disponibilidade usa método PATCH (formulário HTML com `@method('PATCH')`)
- [x] Botão de excluir usa método DELETE com confirmação (atributo `onclick="confirm()"`)
- [x] Contagem no topo atualizada dinamicamente

**Esforço:** P
**Dependências:** PROD-03, PROD-04
