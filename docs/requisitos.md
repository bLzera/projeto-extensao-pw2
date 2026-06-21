# Especificação de Requisitos — Feira Digital

Este documento traduz o propósito e o diagnóstico levantados em
[Propósito e Diagnóstico](./proposito.md) em especificações técnicas — *o que* o
sistema deve fazer, antes de *como* fazer. Segue a etapa **"Definição de Requisitos"**
da Pirâmide de Projeto e serve de contrato entre a necessidade social e a implementação.

A notação adotada:

- **RF** — Requisito Funcional (o que o sistema faz)
- **RNF** — Requisito Não-Funcional (atributos de qualidade e restrições técnicas)
- **RN** — Regra de Negócio (restrições do domínio que o sistema deve garantir)

---

## 1. Atores do Sistema

| Ator         | Autenticação        | Descrição                                                                 |
|--------------|---------------------|---------------------------------------------------------------------------|
| **Visitante**| Nenhuma             | Qualquer pessoa. Navega o catálogo, vê produtos, perfis e avaliações.     |
| **Comprador**| Conta `role=buyer`  | Visitante que se cadastrou. Favorita produtos e avalia produtores.        |
| **Produtor** | Conta `role=producer`| Vendedor. Mantém um perfil de negócio e gerencia o próprio catálogo.     |

Os dois papéis (comprador e produtor) são escolhidos no momento do cadastro e são
mutuamente exclusivos: uma conta é de comprador **ou** de produtor, nunca ambos.

---

## 2. Requisitos Funcionais

### 2.1. Catálogo Público (Visitante)

#### RF-01 — Catálogo público de produtos
O sistema deve exibir uma listagem paginada (12 por página) de todos os produtos
disponíveis, sem exigir autenticação. Cada card exibe foto, nome, preço, unidade de
medida, categoria, nome do produtor e — quando houver — a nota média do produtor.

#### RF-02 — Filtro por categoria
O catálogo deve permitir filtrar produtos por categoria via query string
(`?categoria=frutas`). A categoria selecionada deve ser refletida visualmente no filtro.

#### RF-03 — Filtro por cidade
O catálogo deve permitir filtrar produtos pela cidade do produtor
(`?cidade=Rio do Sul`). A lista de cidades disponíveis é derivada dos produtores que
possuem ao menos um produto disponível.

#### RF-04 — Busca por nome
O catálogo deve oferecer busca por nome de produto via query string (`?busca=tomate`).
Busca, filtro de categoria e filtro de cidade devem ser combináveis simultaneamente.

#### RF-05 — Ordenação do catálogo
O catálogo deve permitir ordenar os resultados (`?ordem=`) por: mais recentes (padrão),
menor preço, maior preço e ordem alfabética (A–Z). A ordenação preserva os demais filtros
ativos na navegação entre páginas.

#### RF-06 — Página de detalhe do produto
Cada produto deve ter uma página própria, acessível por slug
(`/produtos/{slug}`), com foto ampliada, nome, descrição completa, preço, unidade,
categoria, disponibilidade e link para o perfil do produtor.

#### RF-07 — Listagem de produtores
O sistema deve oferecer uma página pública com todos os produtores cadastrados,
exibindo nome do negócio, foto, cidade, nota média e link para o perfil.

#### RF-08 — Perfil público do produtor
Cada produtor deve ter uma página pública (`/produtores/{slug}`) com nome do negócio,
foto, descrição, cidade, dados de contato, a listagem dos seus produtos disponíveis,
a nota média, o feed das avaliações recentes e — para o comprador autenticado — a área
para enviar/editar a própria avaliação.

#### RF-09 — Contato direto via WhatsApp
Quando o produtor cadastrar um número de WhatsApp, o sistema deve gerar um link
`wa.me` com mensagem pré-preenchida e contextual ao produto, permitindo ao visitante
iniciar a negociação diretamente com o produtor.

### 2.2. Conta e Autenticação

#### RF-10 — Cadastro com escolha de papel
O sistema deve permitir o registro informando nome, e-mail, senha e o tipo de conta
(comprador ou produtor). O e-mail deve ser único no sistema.

#### RF-11 — Verificação de e-mail
Após o cadastro, um e-mail de verificação deve ser enviado automaticamente. O acesso
às funcionalidades autenticadas é condicionado à verificação. Usuários não verificados
são redirecionados à tela de aviso.

#### RF-12 — Login, logout e recuperação de senha
O sistema deve oferecer autenticação por e-mail e senha, encerramento de sessão e o
fluxo de recuperação de senha por e-mail.

#### RF-13 — Gerenciamento da própria conta
Todo usuário autenticado deve conseguir editar nome, e-mail e senha, e excluir a
própria conta.

### 2.3. Produtor

#### RF-14 — Setup obrigatório do perfil de negócio
Após verificar o e-mail, o produtor deve completar o perfil do negócio antes de acessar
o dashboard. Obrigatórios: nome do negócio e cidade. Opcionais: descrição, telefone,
WhatsApp, e-mail de contato e foto.

#### RF-15 — Edição do perfil de negócio
O produtor autenticado deve conseguir editar os dados do seu perfil a qualquer momento
pelo dashboard.

#### RF-16 — CRUD de produtos
O produtor autenticado deve conseguir criar, editar e remover produtos
(nome, descrição, preço, unidade, categoria, foto, disponibilidade).

#### RF-17 — Alternar disponibilidade do produto
O produtor deve conseguir marcar/desmarcar um produto como disponível sem precisar
removê-lo. Produtos indisponíveis somem do catálogo público.

#### RF-18 — Destacar produto
O produtor deve conseguir marcar um produto como destaque (`is_featured`), sinalizando-o
com prioridade na vitrine.

#### RF-19 — Curadoria de avaliações recebidas
O produtor deve ter, no dashboard, um painel das avaliações recebidas, com estatísticas
(total, média, ocultas) e a possibilidade de **ocultar/exibir** cada avaliação no feed
público. O produtor não pode editar nem excluir avaliações, e ocultar uma avaliação
**não** altera a nota média.

### 2.4. Comprador

#### RF-20 — Favoritar produtos
O comprador autenticado deve conseguir adicionar e remover produtos da sua lista de
favoritos, a partir dos cards do catálogo ou da página de detalhe.

#### RF-21 — Listar favoritos
O comprador deve ter uma página (`/meus-favoritos`) com seus produtos favoritados.

#### RF-22 — Avaliar produtor
O comprador autenticado deve conseguir avaliar um produtor com nota de 1 a 5 estrelas e
comentário opcional. Cada comprador tem no máximo uma avaliação por produtor.

#### RF-23 — Editar e excluir a própria avaliação
O comprador deve conseguir editar (avaliação marcada como "editada") e excluir a própria
avaliação. A exclusão é lógica (soft delete): o registro é preservado, mas a nota some da
média. Após excluir, o comprador pode avaliar novamente.

### 2.5. Requisitos Transversais

#### RF-24 — Upload e imagens externas
O sistema deve aceitar upload de imagens (produtos e perfil), armazenadas em
`storage/app/public` via link simbólico. O campo de foto também aceita URLs externas
(usadas pelo seed de demonstração), resolvidas de forma transparente.

#### RF-25 — Paginação server-side
Todas as listagens (catálogo, favoritos, avaliações, dashboard) devem ser paginadas
server-side via `paginate()` do Eloquent, com links de navegação que preservam os filtros.

---

## 3. Requisitos Não-Funcionais

#### RNF-01 — Arquitetura MVC
O sistema deve seguir o padrão MVC do Laravel: regras de domínio nos Models, fluxo de
requisição nos Controllers e apresentação nas Views Blade.

#### RNF-02 — Renderização server-side
Toda a geração de HTML deve ocorrer no servidor via Blade. JavaScript no cliente deve ser
mínimo, restrito a interações sem equivalente HTML nativo.

#### RNF-03 — Persistência com MySQL e Eloquent
A persistência deve usar MySQL 8 com o ORM Eloquent. As relações e restrições de
integridade (chaves estrangeiras, unicidade, cascata) devem ser declaradas nas migrations.

#### RNF-04 — Autenticação e segurança
Rotas autenticadas devem ser protegidas por middleware (`auth`, `verified` e o middleware
de perfil de produtor). Todos os formulários usam CSRF token. Dados sensíveis (senha)
são armazenados com hash.

#### RNF-05 — Containerização
O ambiente deve ser totalmente containerizado via Laravel Sail (Docker Compose),
reprodutível em Linux e Windows (WSL2).

#### RNF-06 — Estilização com SCSS
Os estilos devem ser escritos em SCSS, compilados via Vite, organizados por variáveis,
mixins, componentes e páginas — sem framework CSS de terceiros.

#### RNF-07 — Validação server-side
Todos os formulários devem ter validação server-side via Form Requests do Laravel, com
erros exibidos inline nos campos correspondentes.

#### RNF-08 — Documentação
Requisitos, arquitetura, rotas e o histórico de sprints devem ser mantidos no diretório
`docs/`. O `README.md` deve cobrir setup e execução.

---

## 4. Regras de Negócio

#### RN-01 — Acesso público irrestrito de leitura
Nenhuma funcionalidade de leitura (catálogo, detalhe, perfil de produtor, avaliações)
exige conta.

#### RN-02 — Papel definido no cadastro
Toda conta é comprador **ou** produtor, escolhido no registro. Não há troca de papel
nem acúmulo dos dois.

#### RN-03 — E-mail verificado é pré-requisito para operar
Um usuário com e-mail não verificado não acessa funcionalidades autenticadas (favoritar,
avaliar, dashboard ou setup).

#### RN-04 — Perfil completo é pré-requisito para o dashboard
Um produtor com e-mail verificado mas sem perfil de negócio é redirecionado ao setup
antes de qualquer rota de dashboard. Compradores nunca passam pelo setup.

#### RN-05 — Produtor gerencia apenas o próprio catálogo
O sistema deve garantir (via `ProductPolicy`) que um produtor só edite ou remova os
próprios produtos.

#### RN-06 — Papéis têm capacidades exclusivas
Apenas compradores favoritam e avaliam; apenas produtores têm dashboard e catálogo.
Tentativas cruzadas são bloqueadas (403).

#### RN-07 — Produtos indisponíveis ficam ocultos no público
Produtos com `is_available = false` não aparecem no catálogo nem no perfil público,
mas continuam visíveis no dashboard do dono.

#### RN-08 — Uma avaliação por par comprador/produtor
A unicidade `(buyer_id, producer_id)` é garantida no banco. Reavaliar após excluir
reutiliza o mesmo registro.

#### RN-09 — A nota média é imune à curadoria
A média e a contagem consideram apenas avaliações com `status = active`. Ocultar uma
avaliação (`hidden`) afeta só a exibição no feed, nunca a média. O comprador não é
informado de que sua avaliação foi ocultada (decisão intencional de design).

#### RN-10 — Categorias são gerenciadas via seed
As categorias são fixas e inseridas via seeder. Não há UI de gerenciamento de categorias,
e uma categoria não pode ser removida enquanto houver produtos vinculados (`restrictOnDelete`).

---

## 5. Exclusões de Escopo

Os itens abaixo foram conscientemente deixados de fora da entrega. Vários foram desenhados
na [Sprint 9](./sprints/sprint-09.md) como evolução possível, mas não eram pré-requisito
para o valor central do produto e ficaram para versões futuras.

| Item excluído                         | Justificativa                                              |
|---------------------------------------|------------------------------------------------------------|
| Carrinho / checkout / pagamento online| O contato direto produtor↔consumidor é suficiente para o MVP e mantém o projeto fora da complexidade financeira/jurídica de transações |
| Chat / mensagens internas             | Exigiria WebSockets ou polling; o WhatsApp já cobre o contato |
| Formulário de contato por e-mail      | Desenhado na Sprint 9; o link de WhatsApp atende o mesmo objetivo |
| Avaliações por produto                | A avaliação é por produtor (reputação do vendedor), decisão mais aderente ao objetivo social |
| Métricas de visualização / estoque / preço promocional | Recursos de dashboard avançado (Sprint 9), não essenciais ao MVP |
| Painel administrativo / moderação     | Sem aprovação manual de produtores no MVP; a curadoria de avaliações fica com o próprio produtor |
| Gerenciamento de categorias via UI    | Categorias fixas via seed são suficientes                  |
| Notificações push                     | Fora do escopo                                             |
