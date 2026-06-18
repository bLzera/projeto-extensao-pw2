# Rotas da Aplicação

Mapeamento completo de todas as rotas registradas na aplicação Feira Digital (Laravel 11).

---

## Rotas Públicas

Acessíveis por qualquer visitante, sem necessidade de autenticação.

| Método | URI | Controller | Nome | Descrição |
|--------|-----|-----------|------|-----------|
| GET | `/` | `HomeController@index` | `home` | Página inicial com catálogo de produtos |
| GET | `/produtos/{product}` | `ProductController@show` | `products.show` | Detalhes de um produto |
| GET | `/produtores` | `ProducerController@index` | `producers.index` | Listagem de produtores |
| GET | `/produtores/{producer}` | `ProducerController@show` | `producers.show` | Perfil público de um produtor |

---

## Rotas Autenticadas — Setup

Acessíveis apenas para usuários autenticados e com e-mail verificado. Não exigem perfil de produtor completo (evita loop de redirecionamento).

**Middleware:** `auth`, `verified`

| Método | URI | Controller | Nome | Descrição |
|--------|-----|-----------|------|-----------|
| GET | `/setup` | `SetupController@create` | `producer.setup` | Formulário de configuração inicial do perfil do produtor |
| POST | `/setup` | `SetupController@store` | `producer.setup.store` | Salva o perfil do produtor |

---

## Rotas Autenticadas — Dashboard

Acessíveis apenas para usuários autenticados, com e-mail verificado e com perfil de produtor já configurado.

**Middleware:** `auth`, `verified`, `producer.profile`

| Método | URI | Controller | Nome | Descrição |
|--------|-----|-----------|------|-----------|
| GET | `/dashboard` | `DashboardController@index` | `dashboard` | Painel principal do produtor |
| GET | `/dashboard/profile` | `ProducerProfileController@edit` | `producer.profile.edit` | Formulário de edição do perfil do produtor |
| PATCH | `/dashboard/profile` | `ProducerProfileController@update` | `producer.profile.update` | Atualiza o perfil do produtor |
| GET | `/dashboard/produtos/criar` | `DashboardProductController@create` | `producer.products.create` | Formulário de criação de produto |
| POST | `/dashboard/produtos` | `DashboardProductController@store` | `producer.products.store` | Cria um novo produto |
| GET | `/dashboard/produtos/{product}/editar` | `DashboardProductController@edit` | `producer.products.edit` | Formulário de edição de produto |
| PUT | `/dashboard/produtos/{product}` | `DashboardProductController@update` | `producer.products.update` | Atualiza um produto |
| DELETE | `/dashboard/produtos/{product}` | `DashboardProductController@destroy` | `producer.products.destroy` | Remove um produto |
| PATCH | `/dashboard/produtos/{product}/disponibilidade` | `DashboardProductController@toggleAvailability` | `producer.products.toggle` | Alterna a disponibilidade de um produto |

---

## Rotas Autenticadas — Perfil do Usuário (Breeze)

Gerenciamento de conta do usuário autenticado.

**Middleware:** `auth`

| Método | URI | Controller | Nome | Descrição |
|--------|-----|-----------|------|-----------|
| GET | `/profile` | `ProfileController@edit` | `profile.edit` | Formulário de edição da conta |
| PATCH | `/profile` | `ProfileController@update` | `profile.update` | Atualiza os dados da conta |
| DELETE | `/profile` | `ProfileController@destroy` | `profile.destroy` | Remove a conta do usuário |

---

## Rotas de Autenticação (Breeze)

Rotas geradas pelo Laravel Breeze para registro, login e recuperação de senha.

### Grupo `guest` — apenas visitantes não autenticados

| Método | URI | Controller | Nome | Descrição |
|--------|-----|-----------|------|-----------|
| GET | `/register` | `RegisteredUserController@create` | `register` | Formulário de cadastro |
| POST | `/register` | `RegisteredUserController@store` | — | Processa o cadastro |
| GET | `/login` | `AuthenticatedSessionController@create` | `login` | Formulário de login |
| POST | `/login` | `AuthenticatedSessionController@store` | — | Processa o login |
| GET | `/forgot-password` | `PasswordResetLinkController@create` | `password.request` | Formulário de recuperação de senha |
| POST | `/forgot-password` | `PasswordResetLinkController@store` | `password.email` | Envia o e-mail de recuperação |
| GET | `/reset-password/{token}` | `NewPasswordController@create` | `password.reset` | Formulário de redefinição de senha |
| POST | `/reset-password` | `NewPasswordController@store` | `password.store` | Salva a nova senha |

### Grupo `auth` — apenas usuários autenticados

| Método | URI | Controller | Middleware adicional | Nome | Descrição |
|--------|-----|-----------|----------------------|------|-----------|
| GET | `/verify-email` | `EmailVerificationPromptController` | — | `verification.notice` | Aviso de verificação de e-mail pendente |
| GET | `/verify-email/{id}/{hash}` | `VerifyEmailController` | `signed`, `throttle:6,1` | `verification.verify` | Confirma o e-mail via link assinado |
| POST | `/email/verification-notification` | `EmailVerificationNotificationController@store` | `throttle:6,1` | `verification.send` | Reenvia o e-mail de verificação |
| GET | `/confirm-password` | `ConfirmablePasswordController@show` | — | `password.confirm` | Formulário de confirmação de senha |
| POST | `/confirm-password` | `ConfirmablePasswordController@store` | — | — | Processa a confirmação de senha |
| PUT | `/password` | `PasswordController@update` | — | `password.update` | Atualiza a senha do usuário |
| POST | `/logout` | `AuthenticatedSessionController@destroy` | — | `logout` | Encerra a sessão do usuário |
