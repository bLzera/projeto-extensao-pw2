# Arquitetura e Decisões Técnicas — Feira Digital

---

## 1. Decisões Técnicas (ADR)

### ADR-01 — Laravel Sail para containerização
**Decisão:** Usar Laravel Sail como ambiente Docker.
**Motivo:** Sail é a solução oficial do Laravel, provê um `docker-compose.yml` pré-configurado
com PHP, MySQL e Mailpit (para teste de e-mail), e funciona de forma idêntica em Linux e
Windows via WSL2. Elimina o custo de manter um compose customizado para um projeto com
prazo curto.
**Serviços incluídos:** `laravel.test` (PHP 8.5 + app), `mysql` (MySQL 8), `mailpit` (SMTP local).

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
users                                   favorites (pivot buyer↔produto)
┌──────────────────────┐                ┌──────────────────────┐
│ id                   │ PK             │ id                   │ PK
│ name                 │                │ user_id              │ FK→users
│ email                │ unique         │ product_id           │ FK→products
│ role                 │ enum(producer, │ created_at/updated_at│
│                      │  buyer) def.   │ UNIQUE(user_id,      │
│                      │  'producer'    │        product_id)   │
│ email_verified_at    │ nullable       └──────────────────────┘
│ password             │
│ remember_token       │ nullable       ratings (comprador avalia produtor)
│ created_at/updated_at│                ┌──────────────────────┐
└──────┬────────┬──────┘                │ id                   │ PK
       │1       │ (buyer_id)            │ buyer_id             │ FK→users
       │hasOne  └──────────────────────▶│ producer_id          │ FK→producers
       │1                               │ stars                │ tinyint 1–5
┌──────▼───────────────┐                │ comment              │ nullable
│ producers            │◀───────────────│ hidden               │ bool def. false
├──────────────────────┤  (producer_id) │ status               │ enum(active,
│ id                   │ PK             │                      │  deleted)
│ user_id              │ FK→users uniq  │ edited_at            │ nullable
│ farm_name            │                │ created_at/updated_at│
│ slug                 │ unique         │ UNIQUE(buyer_id,     │
│ description          │ nullable       │        producer_id)  │
│ city                 │                └──────────────────────┘
│ phone                │ nullable
│ whatsapp             │ nullable       categories
│ contact_email        │ nullable       ┌──────────────────────┐
│ photo                │ nullable       │ id                   │ PK
│ created_at/updated_at│                │ name                 │
└──────────┬───────────┘                │ slug                 │ unique
           │ 1                          │ created_at/updated_at│
           │ hasMany                    └──────────┬───────────┘
           │ N                                     │ 1 hasMany / N
           │            ┌──────────────────────────▼──────────┐
           └───────────▶│ products                            │
                        ├─────────────────────────────────────┤
                        │ id                       │ PK
                        │ producer_id              │ FK→producers (cascade)
                        │ category_id              │ FK→categories (restrict)
                        │ name                     │
                        │ slug                     │ unique
                        │ description              │ nullable
                        │ price                    │ decimal(10,2)
                        │ unit                     │ ex: kg, unid., dúzia
                        │ photo                    │ nullable (upload ou URL externa)
                        │ is_available             │ boolean, default: true
                        │ is_featured              │ boolean, default: false
                        │ created_at/updated_at    │
                        └─────────────────────────────────────┘
```

**Notas de integridade:**
- `producers.user_id` é único (1 perfil por usuário) e cascateia na exclusão do usuário.
- `products.producer_id` cascateia; `products.category_id` usa `restrictOnDelete` —
  uma categoria não pode ser apagada com produtos vinculados.
- `favorites` e `ratings` têm índice único do par, garantindo no banco "um favorito por
  produto" e "uma avaliação por produtor".
- `slug` em `producers` e `products` é gerado automaticamente no Model (hook `saving`),
  com sufixo numérico para evitar colisão.

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
GET  /                                     HomeController@index
GET  /produtos/{product:slug}              ProductController@show
GET  /produtores                           ProducerController@index
GET  /produtores/{producer:slug}/avaliacoes ProducerController@ratings
GET  /produtores/{producer:slug}           ProducerController@show

// ─── Auth (gerado pelo Breeze; register com campo `role`) ─────────
GET  /register                       RegisteredUserController@create
POST /register                       RegisteredUserController@store   // valida role in:producer,buyer
GET  /login                          AuthenticatedSessionController@create
POST /login                          AuthenticatedSessionController@store
POST /logout                         AuthenticatedSessionController@destroy

// ─── Verificação de E-mail (Breeze) ──────────────────────────────
GET  /verify-email                   EmailVerificationPromptController
GET  /verify-email/{id}/{hash}       VerifyEmailController
POST /email/verification-notification EmailVerificationNotificationController

// ─── Conta do usuário (auth — Breeze) ────────────────────────────
GET    /profile                      ProfileController@edit
PATCH  /profile                      ProfileController@update
DELETE /profile                      ProfileController@destroy

// ─── Setup de Perfil (auth + verified, sem perfil) ───────────────
GET  /setup                          Producer\SetupController@create
POST /setup                          Producer\SetupController@store

// ─── Dashboard (auth + verified + producer.profile) ──────────────
GET  /dashboard                      Producer\DashboardController@index

// ─── Perfil do Produtor — gerenciamento (mesmos guards) ──────────
GET   /dashboard/profile             Producer\ProfileController@edit
PATCH /dashboard/profile             Producer\ProfileController@update

// ─── Produtos — gerenciamento (mesmos guards) ────────────────────
GET    /dashboard/produtos/criar              Producer\ProductController@create
POST   /dashboard/produtos                    Producer\ProductController@store
GET    /dashboard/produtos/{product}/editar   Producer\ProductController@edit
PUT    /dashboard/produtos/{product}          Producer\ProductController@update
DELETE /dashboard/produtos/{product}          Producer\ProductController@destroy
PATCH  /dashboard/produtos/{product}/disponibilidade  Producer\ProductController@toggleAvailability
PATCH  /dashboard/produtos/{product}/destaque         Producer\ProductController@toggleFeatured

// ─── Curadoria de avaliações do produtor (mesmos guards) ─────────
PATCH  /dashboard/avaliacoes/{rating}/visibilidade    Producer\DashboardRatingController@toggle

// ─── Funcionalidades de comprador (auth + verified) ──────────────
POST   /favoritos/{product}                FavoriteController@toggle
GET    /meus-favoritos                     Buyer\FavoritesPageController@index
POST   /produtores/{producer}/avaliar      RatingController@upsert
DELETE /produtores/{producer}/avaliar      RatingController@destroy
```

### Middleware por grupo de rota

| Grupo                   | Middlewares aplicados                              |
|-------------------------|----------------------------------------------------|
| Público                 | nenhum                                             |
| Conta / setup / comprador | `auth`, `verified`                               |
| Dashboard do produtor   | `auth`, `verified`, `producer.profile`             |

O middleware `producer.profile` (alias de `EnsureProducerProfileComplete`) redireciona
compradores para a home e produtores sem perfil para o `/setup`, evitando que cheguem ao
dashboard sem o perfil de negócio completo.

---

## 4. Estrutura de Controllers

O controller base (`app/Http/Controllers/Controller.php`) inclui a trait
`AuthorizesRequests`, que disponibiliza `$this->authorize()` em todos os controllers.
Esse método aciona o sistema de Policies do Laravel para verificar permissões.

```
app/Http/Controllers/
├── Controller.php                   # base: usa AuthorizesRequests
├── HomeController.php               # GET / — catálogo público (filtros, busca, ordenação)
├── ProductController.php            # GET /produtos/{slug}
├── ProducerController.php           # GET /produtores, /produtores/{slug}, .../avaliacoes
├── FavoriteController.php           # POST /favoritos/{product} — toggle (comprador)
├── RatingController.php             # POST|DELETE /produtores/{producer}/avaliar (comprador)
├── ProfileController.php            # conta do usuário (Breeze)
├── Auth/
│   └── ...                          # gerado pelo Breeze (register com campo role)
├── Buyer/
│   └── FavoritesPageController.php  # GET /meus-favoritos
└── Producer/
    ├── DashboardController.php      # GET /dashboard
    ├── ProfileController.php        # GET|PATCH /dashboard/profile
    ├── ProductController.php        # CRUD /dashboard/produtos + toggles
    ├── SetupController.php          # GET|POST /setup
    └── DashboardRatingController.php# PATCH visibilidade de avaliação (curadoria)

app/Http/Middleware/
└── EnsureProducerProfileComplete.php  # alias: producer.profile
```

### Autorização

| Mecanismo                     | Onde                              | Regra                                                        |
|-------------------------------|-----------------------------------|--------------------------------------------------------------|
| `ProductPolicy`               | Producer\ProductController        | `$user->producer?->id === $product->producer_id` (`update`, `delete`) |
| Verificação inline (`abort_if`)| RatingController, DashboardRatingController | comprador só mexe na própria avaliação; produtor só cura avaliações do próprio negócio |
| `isBuyer()` / `isProducer()`  | Controllers de favoritos/avaliação| capacidades exclusivas por papel (tentativa cruzada → 403)   |

`ProductPolicy` é descoberta automaticamente pelo Laravel via convenção de nome
(`Product` → `ProductPolicy`), sem registro manual.

---

## 5. Estrutura SCSS

```
resources/scss/
├── app.scss              # entry point: importa todos os partials
├── _variables.scss       # tokens de cor, tipografia, espaçamento, sombras, easing
├── _mixins.scss          # mixins reutilizáveis (respond-to, flex-center, truncate)
├── _base.scss            # reset, box-sizing, tipografia base (Fraunces em h1/h2), page-fade-in
├── _layout.scss          # container, header, nav, footer, dropdown de perfil
├── _catalog.scss         # hero, cards de produto/produtor, empty states, paginação, filtros
├── _dashboard.scss       # formulários, tabela de produtos, stat cards, botões, badges
├── _public.scss          # detalhe de produto, perfil de produtor
└── _auth.scss            # formulários de login, registro e setup
```

---

## 6. Makefile — Comandos de Desenvolvimento

O projeto expõe todos os comandos frequentes via `make`. Nenhum PHP ou Composer precisam
estar instalados localmente — tudo roda dentro dos containers Sail.

| Comando | Descrição |
|---|---|
| `make install` | Setup completo após clonar: composer (via Docker), `up`, `key:generate`, migrate, seed, storage link, npm install e build |
| `make up` | Sobe os containers em background |
| `make down` | Derruba os containers |
| `make restart` | Derruba e sobe (útil após editar o `.env`) |
| `make logs` | Acompanha logs em tempo real |
| `make bash` | Shell dentro do container da aplicação |
| `make migrate` | Roda migrations pendentes |
| `make seed` | Executa os seeders |
| `make fresh` | `migrate:fresh --seed` — recria o banco do zero |
| `make setup` | `migrate` + `seed` + `storage:link` em sequência |
| `make npm-dev` | Vite em modo watch (hot-reload de SCSS/JS) |
| `make npm-build` | Compila assets para produção |
| `make clear` | Limpa caches de config, view, rota e app |
| `make artisan CMD="..."` | Executa qualquer comando Artisan avulso |
| `make composer CMD="..."` | Executa qualquer comando Composer avulso |

---

## 7. Estrutura de Diretórios do Projeto

```
projeto-extensao-pw2/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # conforme seção 4
│   │   ├── Middleware/
│   │   └── Requests/        # Form Requests de validação
│   ├── Models/
│   │   ├── User.php           # role producer|buyer; favorites(); ratings()
│   │   ├── Producer.php       # slug auto; whatsappUrl(); ratings()/activeRatings()
│   │   ├── Product.php        # slug auto; photo_url (upload ou URL externa)
│   │   ├── Category.php
│   │   └── Rating.php         # avaliação do comprador ao produtor
│   └── Policies/
│       └── ProductPolicy.php  # garante que produtor edita só seus produtos
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── CategorySeeder.php # categorias fixas
│       └── DemoSeeder.php     # produtores, produtos e avaliações de demonstração
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
├── compose.yaml             # gerenciado pelo Sail
└── README.md
```
