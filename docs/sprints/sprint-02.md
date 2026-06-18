# Sprint 2 — Autenticação do Produtor

**Objetivo:** Fluxo completo de onboarding do produtor funcionando — do registro à configuração
do perfil. Ao fim desta sprint, um novo usuário consegue se registrar, verificar o e-mail
via Mailpit, completar o perfil do negócio e acessar o dashboard.

**Duração estimada:** meio dia

---

## Backlog

---

### AUTH-01 — Criar Models com Relacionamentos Eloquent

**Descrição:**
Criar os Models `Producer`, `Product` e `Category` com seus relacionamentos. Ajustar o
Model `User` para incluir o relacionamento com `Producer`.

**Relacionamentos:**
- `User` hasOne `Producer`
- `Producer` belongsTo `User`
- `Producer` hasMany `Product`
- `Product` belongsTo `Producer`
- `Product` belongsTo `Category`
- `Category` hasMany `Product`

**Critérios de aceitação:**
- [ ] 3 Models criados (`Producer`, `Product`, `Category`)
- [ ] `User` atualizado com `hasOne(Producer::class)`
- [ ] `$fillable` definido corretamente em todos os models
- [ ] `$casts` aplicado em `Product` para `price` (decimal) e `is_available` (boolean)
- [ ] Relacionamentos testáveis via `sail artisan tinker`

**Esforço:** P
**Dependências:** INF-03

---

### AUTH-02 — Middleware EnsureProducerProfileComplete

**Descrição:**
Criar o middleware `EnsureProducerProfileComplete` que intercepta requisições às rotas de
dashboard. Lógica: se o usuário autenticado e com e-mail verificado ainda não tem um registro
em `producers`, redirecionar para `GET /setup`. Registrar o middleware e aplicar ao grupo
de rotas do dashboard.

**Critérios de aceitação:**
- [ ] Middleware criado em `app/Http/Middleware/`
- [ ] Registrado no `bootstrap/app.php` (Laravel 11)
- [ ] Aplicado ao grupo de rotas `/dashboard/*`
- [ ] Usuário sem perfil é redirecionado para `/setup`
- [ ] Usuário com perfil não é interceptado

**Esforço:** P
**Dependências:** AUTH-01, INF-02

---

### AUTH-03 — Controller e Views de Setup de Perfil

**Descrição:**
Criar `ProducerSetupController` com dois métodos:
- `create()` — exibe o formulário de setup
- `store()` — valida, cria o registro `Producer` vinculado ao `User` autenticado, e redireciona
  para o dashboard

Este controller deve ser acessível apenas para usuários autenticados e com e-mail verificado,
mas **sem** o middleware de perfil completo (para evitar loop de redirect).

**Form Request: `StoreProducerSetupRequest`**
- `farm_name`: required, string, max:255
- `city`: required, string, max:255
- `description`: nullable, string, max:1000
- `phone`: nullable, string, max:20
- `whatsapp`: nullable, string, max:20
- `contact_email`: nullable, email, max:255
- `photo`: nullable, image, max:2048 (2MB), mimes:jpg,jpeg,png,webp

**Critérios de aceitação:**
- [ ] `ProducerSetupController` criado com `create` e `store`
- [ ] `StoreProducerSetupRequest` com regras de validação
- [ ] Erros de validação exibidos inline na view
- [ ] Upload de foto do perfil salvo em `storage/app/public/producers/`
- [ ] Após `store()`, produtor redirecionado ao dashboard com flash de sucesso
- [ ] Acesso a `/setup` por produtor que já tem perfil redireciona ao dashboard

**Esforço:** M
**Dependências:** AUTH-01, AUTH-02

---

### AUTH-04 — View de Setup de Perfil

**Descrição:**
Criar `resources/views/auth/setup.blade.php`. O formulário deve conter todos os campos
definidos na `StoreProducerSetupRequest`. O campo de foto deve ter preview da imagem
selecionada (comportamento nativo do `<input type="file">`; não requer JS adicional).

**Critérios de aceitação:**
- [ ] View criada extendendo `layouts/app.blade.php`
- [ ] Todos os campos do formulário presentes
- [ ] Mensagens de erro de validação exibidas por campo (`$errors->get('campo')`)
- [ ] Formulário usa `enctype="multipart/form-data"` para suporte a upload
- [ ] CSRF token presente (`@csrf`)

**Esforço:** P
**Dependências:** AUTH-03

---

### AUTH-05 — Dashboard do Produtor (estrutura base)

**Descrição:**
Criar `Producer\DashboardController@index` e a view `dashboard/index.blade.php`. Neste ponto,
o dashboard exibe apenas: boas-vindas ao produtor (com o nome do negócio), um resumo simples
(total de produtos, total disponíveis) e um link para criar o primeiro produto.
O CRUD de produtos completo é implementado na Sprint 3.

**Critérios de aceitação:**
- [ ] Rota `GET /dashboard` protegida pelos três middlewares (auth, verified, profile)
- [ ] View exibe nome do negócio do produtor logado
- [ ] Contagem de produtos disponíveis vs total (pode ser 0/0 neste ponto)
- [ ] Link "Adicionar produto" presente (pode estar 404 até a Sprint 3)

**Esforço:** P
**Dependências:** AUTH-02, AUTH-03

---

### AUTH-06 — Edição de Perfil do Produtor

**Descrição:**
Criar `Producer\ProfileController` com `edit()` e `update()`. O formulário de edição é
idêntico ao setup, mas pré-preenchido com os dados atuais. A foto atual deve ser exibida
ao lado do campo de upload; se um novo arquivo for enviado, o arquivo antigo deve ser
removido do storage.

**Form Request: `UpdateProducerProfileRequest`** (regras idênticas ao setup)

**Critérios de aceitação:**
- [ ] `Producer\ProfileController` com `edit` e `update`
- [ ] View `dashboard/profile/edit.blade.php` com campos pré-preenchidos
- [ ] Foto atual exibida (se existir)
- [ ] Ao atualizar com nova foto, a foto anterior é deletada do storage
- [ ] Atualização sem trocar a foto não exige nova foto (campo opcional no update)
- [ ] Flash de sucesso após atualização

**Esforço:** M
**Dependências:** AUTH-05
