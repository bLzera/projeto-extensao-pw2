# Fluxos de Requisição — Feira Digital

Documento que descreve o ciclo de vida de três requisições representativas do sistema,
do momento em que chegam ao servidor até a resposta enviada ao cliente.

---

## 1. Rota pública simples — `GET /`

**Caso:** visitante acessa o catálogo sem estar autenticado.

```
Cliente (browser)
    │
    │  GET /
    ▼
Nginx (container laravel.test)
    │  Repassa para PHP-FPM → public/index.php
    ▼
Kernel HTTP do Laravel
    │  Executa middlewares globais em sequência:
    │    EncryptCookies, StartSession, ShareErrorsFromSession,
    │    VerifyCsrfToken, SubstituteBindings
    ▼
Router (routes/web.php)
    │  Match: GET / → HomeController@index
    │  Nenhum middleware de grupo — rota pública
    ▼
HomeController@index
    │
    ├─ Category::orderBy('name')->get()
    │    └─ SELECT * FROM categories ORDER BY name
    │
    └─ Product::with(['producer', 'category'])
           ->where('is_available', true)
           ->when($request->categoria, ...)   ← aplica filtro de slug se presente
           ->when($request->busca, ...)        ← aplica LIKE se presente
           ->latest()
           ->paginate(12)
           ->withQueryString()
                └─ SELECT + COUNT em products, JOIN em producers e categories
    │
    ▼
view('home.index', [...])
    │  Blade compila o template:
    │    └─ layouts/app.blade.php
    │         └─ @yield('content') → home/index.blade.php
    │              └─ @foreach → <x-product-card :product="$product" />
    │                   └─ components/product-card.blade.php
    ▼
Resposta HTTP 200 — HTML completo entregue ao browser
```

**Queries executadas:** 2 (categorias + produtos com eager load de produtor e categoria).
**Middlewares de auth:** nenhum.

---

## 2. Rota autenticada — `POST /login`

**Caso:** produtor já cadastrado faz login com e-mail e senha.

```
Cliente (browser)
    │
    │  POST /login   { email, password, remember? }
    ▼
Kernel HTTP
    │  Middlewares globais + VerifyCsrfToken (valida o token do formulário)
    ▼
Router (routes/auth.php)
    │  Match: POST /login → AuthenticatedSessionController@store
    │  Middleware de grupo: guest
    │    └─ Se o usuário já estiver autenticado, redireciona para /dashboard
    ▼
LoginRequest (FormRequest)
    │
    ├─ authorize() → true (sem restrição nesta etapa)
    │
    ├─ rules(): valida email (required, string, email) e password (required, string)
    │    └─ Falha de validação → redireciona de volta ao formulário com $errors
    │
    └─ authenticate():
         │
         ├─ ensureIsNotRateLimited()
         │    └─ RateLimiter verifica chave "email|ip"
         │         Se ≥ 5 tentativas recentes → lança ValidationException com tempo de espera
         │
         ├─ Auth::attempt(['email', 'password'], $remember)
         │    ├─ SELECT * FROM users WHERE email = ?
         │    └─ bcrypt compare (senha digitada vs hash no banco)
         │         Falha → RateLimiter::hit() + ValidationException 'auth.failed'
         │         Sucesso → RateLimiter::clear()
         │
         └─ Usuário autenticado + sessão criada
    │
    ▼
AuthenticatedSessionController@store (continua)
    │
    ├─ $request->session()->regenerate()
    │    └─ Rotaciona o session ID — previne session fixation attack
    │
    └─ redirect()->intended(route('dashboard'))
         └─ Se o middleware auth havia guardado uma URL pretendida anteriormente,
            vai para ela. Caso contrário, vai para /dashboard.
    │
    ▼
Próxima requisição: GET /dashboard
    │  Middlewares do grupo: auth, verified, producer.profile
    │    auth            → usuário está autenticado? ✓
    │    verified        → email verificado? ✓
    │    producer.profile → existe registro em producers para este user_id?
    │                       Não → redireciona para /setup
    │                       Sim → segue para DashboardController@index
    ▼
Resposta HTTP 302 → Location: /dashboard
```

---

## 3. Rota mais complexa — `PUT /dashboard/produtos/{product}`

**Caso:** produtor edita um produto seu, com troca de foto.

Esta rota concentra o maior número de camadas: três middlewares de grupo,
route model binding, FormRequest com pré-processamento, autorização por Policy,
manipulação de arquivo no disco e atualização no banco.

```
Cliente (browser)
    │
    │  PUT /dashboard/produtos/42   (multipart/form-data)
    │    { name, description, price, unit, category_id, is_available, photo }
    ▼
Kernel HTTP
    │  VerifyCsrfToken valida o token _method=PUT embutido no form
    ▼
Router (routes/web.php)
    │  Match: PUT /dashboard/produtos/{product}
    │         → Producer\ProductController@update
    │
    │  Middleware de grupo executados em sequência:
    │
    ├─ auth
    │    └─ Verifica sessão ativa. Se não autenticado → redireciona para /login
    │       (guarda a URL pretendida para usar após o login)
    │
    ├─ verified
    │    └─ Verifica users.email_verified_at IS NOT NULL.
    │       Não verificado → redireciona para /verify-email
    │
    └─ producer.profile  (EnsureProducerProfileComplete)
         └─ SELECT * FROM producers WHERE user_id = ?
            Não encontrado → redireciona para /setup
    │
    ▼
Route Model Binding
    │  {product} = 42
    │  SELECT * FROM products WHERE id = 42
    │  Não encontrado → HTTP 404
    ▼
UpdateProductRequest (FormRequest)
    │
    ├─ authorize() → true
    │    (a autorização real de ownership será feita pelo Policy abaixo)
    │
    ├─ prepareForValidation()
    │    ├─ Converte vírgula em ponto no campo price ("12,50" → "12.50")
    │    └─ Normaliza is_available para boolean (checkbox pode chegar ausente)
    │
    └─ rules(): valida todos os campos
         │  name: required, string, max:255
         │  description: nullable, string, max:2000
         │  price: required, numeric, min:0.01, decimal:0,2
         │  unit: required, string, max:50
         │  category_id: required, exists:categories,id  ← query extra no banco
         │  is_available: nullable, boolean
         │  photo: nullable, image, max:2048, mimes:jpg,jpeg,png,webp
         └─ Falha → redireciona de volta ao formulário com $errors
    │
    ▼
Producer\ProductController@update
    │
    ├─ $this->authorize('update', $product)
    │    └─ Aciona ProductPolicy@update(User $user, Product $product)
    │         └─ $user->producer?->id === $product->producer_id
    │              Falso → HTTP 403 Forbidden
    │              Verdadeiro → segue
    │
    ├─ $data = $request->safe()->except('photo')
    │    └─ Campos validados, sem o arquivo
    │
    ├─ $request->hasFile('photo') → true (produtor enviou nova foto)
    │    ├─ Storage::disk('public')->delete($product->photo)
    │    │    └─ Remove o arquivo antigo de storage/app/public/products/
    │    └─ $request->file('photo')->storeAs('products', Str::uuid().'.webp', 'public')
    │         └─ Salva o novo arquivo com nome UUID em storage/app/public/products/
    │            Atualiza $data['photo'] com o novo path
    │
    └─ $product->update($data)
         └─ UPDATE products SET name=?, price=?, ..., photo=? WHERE id = 42
    │
    ▼
redirect()->route('dashboard')->with('success', 'Produto atualizado com sucesso!')
    │  Flash message gravada na sessão
    ▼
Resposta HTTP 302 → Location: /dashboard
```

**Queries executadas:** 4+
1. `SELECT` do usuário autenticado (middleware auth, via sessão)
2. `SELECT` do producer pelo user_id (middleware producer.profile)
3. `SELECT` do product pelo id (route model binding)
4. `SELECT` para validar `exists:categories,id` (FormRequest)
5. `SELECT` do producer via `$user->producer` (Policy)
6. `UPDATE` no product (controller)

**Camadas de proteção em ordem:**
`auth` → `verified` → `producer.profile` → `FormRequest` (validação) → `ProductPolicy` (ownership)

---

## Comparativo

| Aspecto                | GET /          | POST /login        | PUT /dashboard/produtos/{id} |
|------------------------|----------------|--------------------|------------------------------|
| Middlewares de grupo   | nenhum         | guest              | auth, verified, producer.profile |
| FormRequest            | não            | LoginRequest       | UpdateProductRequest         |
| Pré-processamento      | não            | não                | prepareForValidation()       |
| Policy / autorização   | não            | não                | ProductPolicy@update         |
| Queries ao banco       | 2              | 1–2                | 5–6                          |
| Manipulação de arquivo | não            | não                | sim (delete + store)         |
| Tipo de resposta       | 200 HTML       | 302 redirect       | 302 redirect                 |
