# Sprint 1 — Infraestrutura e Fundação

**Objetivo:** Ambiente Docker funcionando, banco de dados migrado e com seed, layout base Blade
renderizando e pipeline de SCSS compilando. Ao fim desta sprint, `make up` sobe
o ambiente completo sem erros.

**Duração estimada:** meio dia

---

## Backlog

---

### INF-01 — Criar projeto Laravel com Sail

**Descrição:**
Instalar o projeto Laravel via `curl` ou `composer create-project`, adicionando o Sail logo
em seguida. Configurar o `docker-compose.yml` do Sail para incluir os serviços:
`mysql`, `mailpit` (SMTP local para teste de e-mail). Ajustar `.env` com as credenciais
de banco corretas para o ambiente Sail.

**Critérios de aceitação:**
- [ ] Projeto Laravel 11 criado no diretório raiz
- [ ] `make up` sobe os containers sem erros
- [ ] `./vendor/bin/sail artisan --version` retorna a versão correta
- [ ] Serviços `mysql` e `mailpit` acessíveis nos containers
- [ ] `.env.example` atualizado com variáveis do Sail documentadas

**Esforço:** P
**Dependências:** nenhuma

---

### INF-02 — Instalar e configurar Laravel Breeze (Blade)

**Descrição:**
Instalar o pacote `laravel/breeze` via Composer e executar o scaffold com a variante Blade
(`php artisan breeze:install blade`). Isso gera os controllers de auth, rotas e views padrão.
Configurar `MustVerifyEmail` na model `User` para habilitar a verificação de e-mail.

**Critérios de aceitação:**
- [ ] `laravel/breeze` instalado e scaffold executado
- [ ] Rotas de auth acessíveis (`/register`, `/login`, `/verify-email`)
- [ ] `User` implements `MustVerifyEmail`
- [ ] Driver de e-mail no `.env` apontando para Mailpit (`MAIL_HOST=mailpit`, `MAIL_PORT=1025`)
- [ ] Mailpit acessível em `http://localhost:8025` para inspeção de e-mails

**Esforço:** P
**Dependências:** INF-01

---

### INF-03 — Criar Migrations

**Descrição:**
Criar as migrations para as tabelas do projeto na ordem correta (respeitando FK constraints):
`categories`, `producers`, `products`. A tabela `users` já é criada pelo Laravel/Breeze.

**Detalhes das migrations:**

`create_categories_table`
- `id` bigint PK
- `name` varchar(100)
- `slug` varchar(100) unique
- timestamps

`create_producers_table`
- `id` bigint PK
- `user_id` bigint FK → users.id (unique, cascade delete)
- `farm_name` varchar(255)
- `description` text nullable
- `city` varchar(255)
- `phone` varchar(20) nullable
- `whatsapp` varchar(20) nullable
- `contact_email` varchar(255) nullable
- `photo` varchar(255) nullable
- timestamps

`create_products_table`
- `id` bigint PK
- `producer_id` bigint FK → producers.id (cascade delete)
- `category_id` bigint FK → categories.id (restrict)
- `name` varchar(255)
- `description` text nullable
- `price` decimal(10,2)
- `unit` varchar(50) — ex: "kg", "unidade", "dúzia", "caixa"
- `photo` varchar(255) nullable
- `is_available` boolean default true
- timestamps

**Critérios de aceitação:**
- [ ] 3 migrations criadas com estrutura correta
- [ ] `./vendor/bin/sail artisan migrate` executa sem erros
- [ ] FKs com cascade delete configurados corretamente
- [ ] Rollback (`migrate:rollback`) funciona sem erros

**Esforço:** P
**Dependências:** INF-01

---

### INF-04 — Criar Seeder de Categorias

**Descrição:**
Criar `CategorySeeder` com as 10 categorias fixas do MVP e registrá-lo no `DatabaseSeeder`.

**Categorias:**
frutas, verduras, legumes, laticínios, ovos, mel e derivados, grãos e cereais, conservas,
artesanato, outros.

**Critérios de aceitação:**
- [ ] `CategorySeeder` criado com `upsert` (idempotente, pode rodar múltiplas vezes)
- [ ] Registrado em `DatabaseSeeder::run()`
- [ ] `./vendor/bin/sail artisan db:seed` popula a tabela `categories`

**Esforço:** P
**Dependências:** INF-03

---

### INF-05 — Configurar Vite + SCSS

**Descrição:**
Instalar o pacote `sass` via npm. Criar a estrutura de diretórios SCSS em `resources/scss/`
conforme definido em `docs/arquitetura.md` (seção 5). Configurar o `vite.config.js` para
apontar para o entry point `resources/scss/app.scss`. Criar os arquivos base com variáveis
e tokens de design.

**Variáveis SCSS iniciais (`_variables.scss`):**
- Paleta de cores: primária (verde terra), secundária (laranja colheita), neutros
- Tipografia: família, tamanhos base, pesos
- Espaçamentos: escala de 4px (4, 8, 12, 16, 24, 32, 48, 64)
- Breakpoints: `sm: 576px`, `md: 768px`, `lg: 992px`, `xl: 1200px`

**Critérios de aceitação:**
- [ ] Pacote `sass` instalado no `package.json`
- [ ] Estrutura de diretórios SCSS criada
- [ ] `vite.config.js` configurado para o entry SCSS
- [ ] `npm run dev` compila sem erros
- [ ] Variáveis base definidas em `_variables.scss`

**Esforço:** P
**Dependências:** INF-01

---

### INF-06 — Criar Layout Base Blade

**Descrição:**
Criar o layout master `resources/views/layouts/app.blade.php` que será extendido por todas
as views. O layout inclui: `<head>` com meta tags e import do CSS compilado, header com
navegação (logo, link para catálogo, link para produtores, botão de login/logout condicional),
área de conteúdo (`@yield('content')`), footer com informações do projeto.

Criar também uma view de teste (`/`) que extende o layout para validar o pipeline completo.

**Critérios de aceitação:**
- [ ] `layouts/app.blade.php` criado com header, main e footer
- [ ] Navegação exibe "Entrar" para visitantes e "Dashboard / Sair" para produtores logados
- [ ] CSS compilado pelo Vite é carregado corretamente
- [ ] Acesso a `http://localhost` renderiza a página sem erros 500

**Esforço:** P
**Dependências:** INF-02, INF-05

---

### INF-07 — Configurar Storage Link

**Descrição:**
Executar `php artisan storage:link` para criar o link simbólico entre `storage/app/public`
e `public/storage`, habilitando o acesso público às imagens enviadas via upload.
Documentar o comando no README para que seja executado no setup do ambiente.

**Critérios de aceitação:**
- [ ] Link simbólico `public/storage` → `storage/app/public` criado
- [ ] Comando documentado no README em passo de setup (`make storage`)

**Esforço:** P
**Dependências:** INF-01

---

### INF-08 — Makefile de conveniência

**Descrição:**
Criar `Makefile` na raiz do projeto com atalhos para os comandos mais usados do Sail,
eliminando a necessidade de digitar `./vendor/bin/sail` a cada comando.

**Targets:**

| Comando        | Equivale a                                  |
|----------------|---------------------------------------------|
| `make up`      | `sail up -d`                                |
| `make down`    | `sail down`                                 |
| `make restart` | `sail down && sail up -d`                   |
| `make logs`    | `sail logs -f`                              |
| `make bash`    | `sail shell`                                |
| `make tinker`  | `sail artisan tinker`                       |
| `make migrate` | `sail artisan migrate`                      |
| `make seed`    | `sail artisan db:seed`                      |
| `make fresh`   | `sail artisan migrate:fresh --seed`         |
| `make storage` | `sail artisan storage:link`                 |
| `make setup`   | migrate + seed + storage (pós-clone)        |
| `make npm-install` | `sail npm install`                      |
| `make npm-dev` | `sail npm run dev`                          |
| `make npm-build` | `sail npm run build`                      |

**Critérios de aceitação:**
- [ ] `Makefile` criado na raiz do projeto
- [ ] Todos os targets funcionam após `make up`
- [ ] `make setup` executa migrate, seed e storage:link em sequência
- [ ] Targets declarados como `.PHONY`

**Esforço:** P
**Dependências:** INF-01
