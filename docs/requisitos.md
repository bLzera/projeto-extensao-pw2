# Requisitos do Sistema — Feira Digital

---

## 1. Requisitos Funcionais

### RF-01 — Catálogo Público de Produtos
O sistema deve exibir uma listagem paginada de todos os produtos disponíveis sem exigir autenticação.
Cada card de produto deve exibir: foto, nome, preço, unidade de medida, categoria e nome do produtor.

### RF-02 — Filtro por Categoria
O catálogo deve permitir filtrar produtos por categoria via parâmetro de query string (`?categoria=frutas`).
A seleção de categoria deve ser refletida visualmente na interface (estado ativo no filtro).

### RF-03 — Busca de Produtos
O catálogo deve oferecer um campo de busca por nome de produto via query string (`?busca=tomate`).
Filtro e busca devem ser combináveis simultaneamente.

### RF-04 — Página de Detalhe do Produto
Cada produto deve ter uma página própria com: foto ampliada, nome, descrição completa,
preço, unidade, categoria, disponibilidade e um link para o perfil do produtor.

### RF-05 — Perfil Público do Produtor
Cada produtor deve ter uma página pública com: nome do negócio, foto, descrição, cidade,
dados de contato (telefone, WhatsApp, e-mail) e a listagem dos seus produtos disponíveis.

### RF-06 — Listagem de Produtores
O sistema deve oferecer uma página com a listagem de todos os produtores cadastrados,
com nome do negócio, foto, cidade e link para o perfil.

### RF-07 — Cadastro de Produtor
O sistema deve permitir que um usuário se registre informando nome completo, e-mail e senha.
Após o cadastro, um e-mail de verificação deve ser enviado automaticamente.

### RF-08 — Verificação de E-mail
O acesso ao dashboard e às funcionalidades de produtor deve ser condicionado à verificação
do e-mail cadastrado. Usuários não verificados devem ser redirecionados à tela de aviso.

### RF-09 — Setup de Perfil do Produtor
Após a verificação de e-mail, o usuário deve ser obrigado a completar o perfil do seu negócio
antes de acessar o dashboard. Campos obrigatórios: nome do negócio, cidade.
Campos opcionais: descrição, telefone, WhatsApp, e-mail de contato, foto do negócio.

### RF-10 — Edição de Perfil do Produtor
O produtor autenticado deve conseguir editar os dados do seu perfil a qualquer momento
pelo dashboard.

### RF-11 — CRUD de Produtos
O produtor autenticado deve conseguir:
- Criar um novo produto (nome, descrição, preço, unidade, categoria, foto, disponibilidade)
- Editar um produto existente
- Remover um produto
- Alternar a disponibilidade de um produto (disponível / indisponível)

### RF-12 — Upload de Imagens
O sistema deve aceitar upload de imagens para fotos de produtos e de perfil do produtor.
As imagens devem ser armazenadas localmente via `storage/app/public` com link simbólico.

### RF-13 — Paginação Server-Side
O catálogo público e a listagem de produtos no dashboard devem ser paginados server-side
via `paginate()` do Eloquent, com navegação por links de página.

---

## 2. Requisitos Não-Funcionais

### RNF-01 — Arquitetura MVC
O sistema deve seguir estritamente o padrão MVC do Laravel: lógica de negócio nos Models,
fluxo de requisição nos Controllers e apresentação nas Views Blade.

### RNF-02 — Renderização Server-Side
Toda a geração de HTML deve ocorrer no servidor via Blade. O uso de JavaScript no cliente
deve ser mínimo e restrito a interações que não possam ser resolvidas via SSR.

### RNF-03 — Containerização
O ambiente de desenvolvimento e execução deve ser totalmente containerizado via Laravel Sail
(Docker Compose), garantindo reprodutibilidade em ambientes Linux e Windows (WSL2).

### RNF-04 — Autenticação e Segurança
Rotas de dashboard e gerenciamento devem ser protegidas por middleware de autenticação
e verificação de e-mail. Formulários devem usar CSRF token do Laravel.

### RNF-05 — Estilização com SCSS
Os estilos devem ser escritos em SCSS, compilados via Vite. A estrutura SCSS deve seguir
separação por variáveis, mixins, componentes e páginas.

### RNF-06 — Documentação
Todas as decisões técnicas, configurações de ambiente e estrutura de rotas devem ser
documentadas neste diretório `docs/`. O `README.md` do projeto deve cobrir setup e execução.

### RNF-07 — Validação de Dados
Todos os formulários devem ter validação server-side via Laravel Form Requests.
Erros de validação devem ser exibidos inline nos campos correspondentes.

---

## 3. Regras de Negócio

### RN-01 — Acesso público irrestrito
Nenhuma funcionalidade de leitura (catálogo, detalhe, perfil de produtor) exige conta.

### RN-02 — Somente produtores se cadastram
Não existe conta de "comprador". Todo usuário registrado no sistema é um produtor.

### RN-03 — E-mail verificado é pré-requisito para operar
Um produtor com e-mail não verificado não pode acessar o dashboard nem o setup de perfil.

### RN-04 — Perfil completo é pré-requisito para o dashboard
Um produtor com e-mail verificado, mas sem perfil cadastrado, deve ser redirecionado
ao setup de perfil antes de qualquer outra rota autenticada.

### RN-05 — Produtor gerencia apenas seus próprios produtos
O sistema deve garantir, a nível de controller, que um produtor não consiga editar
ou remover produtos de outro produtor (autorização por policy ou verificação manual).

### RN-06 — Produtos indisponíveis ficam ocultos no catálogo público
Produtos com `is_available = false` não aparecem no catálogo nem no perfil público
do produtor, mas continuam visíveis no dashboard do próprio produtor.

### RN-07 — Categorias são gerenciadas via seed
No MVP, as categorias são fixas e inseridas via `DatabaseSeeder`. Não existe UI de
gerenciamento de categorias.

---

## 4. Exclusões de Escopo (MVP)

Os itens abaixo foram conscientemente excluídos para viabilizar a entrega dentro do prazo.
Podem ser implementados em versões futuras.

| Item excluído                         | Justificativa                                           |
|---------------------------------------|---------------------------------------------------------|
| Carrinho de compras / checkout        | Contato direto pelo produtor é suficiente para o MVP    |
| Pagamento online                      | Fora do escopo acadêmico e operacional do MVP           |
| Chat / mensagens internas             | Exigiria WebSockets ou polling; complexidade desnecessária |
| Avaliações de produtos e produtores   | Requer moderação; fora do MVP                           |
| Painel administrativo                 | Não há aprovação manual de produtores                   |
| Gerenciamento de categorias via UI    | Categorias fixas via seed são suficientes               |
| Notificações push                     | Fora do escopo                                          |
