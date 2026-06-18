# Arquitetura e Decisões Técnicas — Feira Digital

---

## 1. Decisões Técnicas (ADR)

### ADR-01 — Laravel Sail para containerização
**Decisão:** Usar Laravel Sail como ambiente Docker.
**Motivo:** Sail é a solução oficial do Laravel, provê um `docker-compose.yml` pré-configurado
com PHP, MySQL e Mailpit (para teste de e-mail), e funciona de forma idêntica em Linux e
Windows via WSL2. Elimina o custo de manter um compose customizado para um projeto com
prazo curto.
**Serviços incluídos:** `laravel.test` (PHP 8.3 + app), `mysql` (MySQL 8), `mailpit` (SMTP local).

### ADR-02 — Laravel Breeze para autenticação
**Decisão:** Usar o scaffold do Laravel Breeze (variante Blade).
**Motivo:** Gera controllers, views e rotas de auth (register, login, logout, email verification,
password reset) prontos e integrados ao Laravel. Evita reimplementar fluxos críticos de
segurança a mão. A variante Blade mantém a consistência com o SSR e sem dependência de
frameworks JS.

### ADR-03 — SCSS compilado via Vite
**Decisão:** Escrever estilos em SCSS, compilado pelo Vite (bundler padrão do Laravel 11).
**Motivo:** SCSS oferece variáveis nativas, aninhamento e mixins, reduzindo repetição de CSS.
O Vite já está incluído no Laravel e aceita SCSS com a adição do pacote `sass`.
Não será utilizado nenhum framework CSS (Bootstrap, Tailwind) para evitar dependências
desnecessárias e manter controle total sobre o design.

### ADR-04 — Armazenamento local de imagens
**Decisão:** Imagens de produtos e de perfil são armazenadas via `storage/app/public`
com link simbólico (`php artisan storage:link`).
**Motivo:** Simples, sem dependência de serviço externo (S3, Cloudinary) e suficiente para
o MVP. Em produção futura, a abstração do filesystem do Laravel permite migrar para um CDN
alterando apenas a configuração de disco em `config/filesystems.php`.

### ADR-05 — Paginação server-side
**Decisão:** Usar `paginate()` do Eloquent em todas as listagens.
**Motivo:** Consistente com a abordagem SSR do projeto. Evita carregar todos os registros
em memória e não exige JavaScript no cliente.

### ADR-06 — Mínimo de JavaScript no cliente
**Decisão:** Nenhum framework JS (Vue, React, Alpine) será utilizado.
**Motivo:** O projeto é SSR puro com Blade. JS no cliente fica restrito a comportamentos
que não têm equivalente HTML nativo (ex: preview de imagem antes do upload, se necessário).

---

## 2. Modelo de Dados

### Diagrama de Entidades

```
users
┌──────────────────────┐
│ id                   │ PK
│ name                 │
│ email                │ unique
│ email_verified_at    │ nullable
│ password             │
│ remember_token       │ nullable
│ created_at           │
│ updated_at           │
└──────────┬───────────┘
           │ 1
           │ hasOne
           │ 1
┌──────────▼───────────┐         ┌──────────────────────┐
│ producers            │         │ categories           │
├──────────────────────┤         ├──────────────────────┤
│ id                   │ PK      │ id                   │ PK
│ user_id              │ FK→users│ name                 │
│ farm_name            │         │ slug                 │ unique
│ description          │ nullable│ created_at           │
│ city                 │         │ updated_at           │
│ phone                │ nullable└──────────────────────┘
│ whatsapp             │ nullable          │ 1
│ contact_email        │ nullable          │ hasMany
│ photo                │ nullable          │ N
│ created_at           │    ┌──────────────▼───────────┐
│ updated_at           │    │ products                 │
└──────────┬───────────┘    ├──────────────────────────┤
           │ 1              │ id                       │ PK
           │ hasMany        │ producer_id              │ FK→producers
           │ N              │ category_id              │ FK→categories
           └────────────────│ name                     │
                            │ description              │ nullable
                            │ price                    │ decimal(10,2)
                            │ unit                     │ ex: kg, unid., dúzia
                            │ photo                    │ nullable
                            │ is_available             │ boolean, default: true
                            │ created_at               │
                            │ updated_at               │
                            └──────────────────────────┘
```

### Categorias (Seed)

| slug         | name              |
|--------------|-------------------|
| frutas       | Frutas            |
| verduras     | Verduras          |
| legumes      | Legumes           |
| laticinios   | Laticínios        |
| ovos         | Ovos              |
| mel          | Mel e Derivados   |
| graos        | Grãos e Cereais   |
| conservas    | Conservas         |
| artesanatos  | Artesanato        |
| outros       | Outros            |

---

## 3. Estrutura de Rotas

```
// ─── Rotas Públicas (sem autenticação) ───────────────────────────
GET  /                               HomeController@index
GET  /produtos/{product}             ProductController@show
GET  /produtores                     ProducerController@index
GET  /produtores/{producer}          ProducerController@show

// ─── Auth (gerado pelo Breeze) ────────────────────────────────────
GET  /register                       RegisteredUserController@create
POST /register                       RegisteredUserController@store
GET  /login                          AuthenticatedSessionController@create
POST /login                          AuthenticatedSessionController@store
POST /logout                         AuthenticatedSessionController@destroy

// ─── Verificação de E-mail (Breeze) ──────────────────────────────
GET  /verify-email                   EmailVerificationPromptController
GET  /verify-email/{id}/{hash}       VerifyEmailController
POST /email/verification-notification EmailVerificationNotificationController

// ─── Setup de Perfil (auth + verified, sem perfil) ───────────────
GET  /setup                          ProducerSetupController@create
POST /setup                          ProducerSetupController@store

// ─── Dashboard (auth + verified + perfil completo) ───────────────
GET  /dashboard                      DashboardController@index

// ─── Perfil do Produtor — gerenciamento (mesmos guards) ──────────
GET   /dashboard/profile             Producer\ProfileController@edit
PATCH /dashboard/profile             Producer\ProfileController@update

// ─── Produtos — gerenciamento (mesmos guards) ────────────────────
GET    /dashboard/produtos/criar              Producer\ProductController@create
POST   /dashboard/produtos                    Producer\ProductController@store
GET    /dashboard/produtos/{product}/editar   Producer\ProductController@edit
PUT    /dashboard/produtos/{product}          Producer\ProductController@update
DELETE /dashboard/produtos/{product}          Producer\ProductController@destroy
PATCH  /dashboard/produtos/{product}/disponibilidade
                                              Producer\ProductController@toggleAvailability
```

### Middleware por grupo de rota

| Grupo            | Middlewares aplicados                              |
|------------------|----------------------------------------------------|
| Público          | nenhum                                             |
| Auth + verified  | `auth`, `verified`                                 |
| Dashboard        | `auth`, `verified`, `EnsureProducerProfileComplete`|

---

## 4. Estrutura de Controllers

```
app/Http/Controllers/
├── HomeController.php               # GET / — catálogo público
├── ProductController.php            # GET /produtos/{product}
├── ProducerController.php           # GET /produtores, /produtores/{producer}
├── Auth/
│   ├── ...                          # gerado pelo Breeze
│   └── ProducerSetupController.php  # GET|POST /setup
├── Producer/
│   ├── DashboardController.php      # GET /dashboard
│   ├── ProfileController.php        # GET|PUT /dashboard/perfil
│   └── ProductController.php        # CRUD /dashboard/produtos
└── Middleware/
    └── EnsureProducerProfileComplete.php
```

---

## 5. Estrutura SCSS

```
resources/scss/
├── app.scss              # entry point: importa todos os partials
├── _variables.scss       # tokens de cor, tipografia, espaçamento, breakpoints
├── _mixins.scss          # mixins reutilizáveis (ex: respond-to, flex-center)
├── _base.scss            # reset, box-sizing, tipografia base, links
├── _layout.scss          # container, header, nav, footer, grid geral
├── _components/
│   ├── _buttons.scss
│   ├── _cards.scss       # card de produto, card de produtor
│   ├── _forms.scss       # inputs, labels, mensagens de erro
│   ├── _badges.scss      # badge de categoria, badge de disponibilidade
│   ├── _pagination.scss
│   └── _alerts.scss      # flash messages (sucesso, erro)
└── _pages/
    ├── _home.scss
    ├── _product.scss
    ├── _producer.scss
    ├── _dashboard.scss
    └── _auth.scss
```

---

## 6. Estrutura de Diretórios do Projeto

```
projeto-extensao-pw2/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # conforme seção 4
│   │   ├── Middleware/
│   │   └── Requests/        # Form Requests de validação
│   ├── Models/
│   │   ├── User.php
│   │   ├── Producer.php
│   │   ├── Product.php
│   │   └── Category.php
│   └── Policies/
│       └── ProductPolicy.php  # garante que produtor edita só seus produtos
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── CategorySeeder.php
├── resources/
│   ├── scss/                # conforme seção 5
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── components/      # componentes Blade reutilizáveis
│       │   ├── product-card.blade.php
│       │   └── producer-card.blade.php
│       ├── home/
│       │   └── index.blade.php
│       ├── products/
│       │   └── show.blade.php
│       ├── producers/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── dashboard/
│       │   ├── index.blade.php
│       │   ├── profile/
│       │   │   └── edit.blade.php
│       │   └── products/
│       │       ├── index.blade.php
│       │       └── form.blade.php   # compartilhado entre create e edit
│       └── auth/                    # gerado pelo Breeze
├── routes/
│   └── web.php
├── docs/                    # este diretório
├── docker-compose.yml       # gerenciado pelo Sail
└── README.md
```
