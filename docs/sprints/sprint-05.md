# Sprint 5 — Interface, Validações e Documentação

**Objetivo:** Sistema visualmente consistente, validações robustas com feedback ao usuário,
e documentação completa para entrega da atividade. Ao fim desta sprint, o projeto está
pronto para ser entregue ao professor.

**Duração estimada:** meio dia a um dia

---

## Backlog

---

### UI-01 — Design System SCSS (variáveis, mixins, base)

**Descrição:**
Implementar os arquivos base do design system SCSS definido em `docs/arquitetura.md` (seção 5).
O foco é criar os tokens e componentes reutilizáveis que serão consumidos pelas páginas.

**`_variables.scss` — paleta sugerida (tema "feira/natureza"):**
```scss
// Cores
$color-primary:    #3a7d44;   // verde floresta
$color-secondary:  #e07b39;   // laranja colheita
$color-bg:         #f9f6f1;   // bege claro
$color-text:       #2d2d2d;
$color-text-muted: #6b6b6b;
$color-border:     #ddd8d0;
$color-success:    #2d6a4f;
$color-danger:     #c0392b;

// Tipografia
$font-family-base: 'Segoe UI', system-ui, sans-serif;
$font-size-base:   1rem;        // 16px
$font-size-sm:     0.875rem;
$font-size-lg:     1.125rem;
$font-size-xl:     1.5rem;
$font-size-2xl:    2rem;

// Espaçamento (escala de 4px)
$space-1: 4px;   $space-2: 8px;
$space-3: 12px;  $space-4: 16px;
$space-5: 24px;  $space-6: 32px;
$space-7: 48px;  $space-8: 64px;

// Breakpoints
$bp-sm: 576px;  $bp-md: 768px;
$bp-lg: 992px;  $bp-xl: 1200px;

// Bordas e sombras
$border-radius:   8px;
$border-radius-sm: 4px;
$shadow-sm:  0 1px 3px rgba(0,0,0,.08);
$shadow-md:  0 4px 12px rgba(0,0,0,.12);
```

**`_mixins.scss` — mixins essenciais:**
```scss
@mixin respond-to($bp) { ... }   // media query helper
@mixin flex-center { ... }        // display:flex + align/justify center
@mixin truncate { ... }           // overflow:hidden + ellipsis
@mixin visually-hidden { ... }    // acessibilidade
```

**Critérios de aceitação:**
- [ ] `_variables.scss` com todos os tokens definidos
- [ ] `_mixins.scss` com os 4 mixins acima
- [ ] `_base.scss` com reset, box-sizing e tipografia base
- [ ] `app.scss` importando todos os partials na ordem correta
- [ ] `npm run build` compila sem warnings

**Esforço:** P
**Dependências:** INF-05

---

### UI-02 — Estilização do Layout e Navegação

**Descrição:**
Estilizar `layouts/app.blade.php`: header fixo com logo e navegação, área de conteúdo com
container centralizado, footer. Estilizar os flash messages (alertas de sucesso/erro que
aparecem via `session()->flash()`).

**Critérios de aceitação:**
- [ ] Header com logo, links de nav e área de auth (login/logout)
- [ ] Container com max-width e padding horizontal responsivos
- [ ] Flash messages visíveis e estilizadas (verde = sucesso, vermelho = erro)
- [ ] Footer com nome do projeto e informação da disciplina
- [ ] Layout responsivo: nav colapsa em mobile (pode ser menu simples, sem JS)

**Esforço:** P
**Dependências:** UI-01

---

### UI-03 — Estilização do Catálogo e Cards

**Descrição:**
Estilizar a home (catálogo), o componente `product-card`, o menu de categorias e os links
de paginação. Resultado esperado: catálogo com visual limpo e legível, hierarquia clara entre
foto, nome e preço do produto.

**Critérios de aceitação:**
- [ ] Grid de produtos responsivo (1 coluna mobile, 2 tablet, 3-4 desktop)
- [ ] Card com foto em proporção fixa (`aspect-ratio: 4/3`), sem distorção
- [ ] Imagem placeholder estilizada quando produto não tem foto
- [ ] Menu de categorias com estado ativo destacado
- [ ] Links de paginação estilizados e espaçados
- [ ] Campo de busca estilizado e funcional visualmente

**Esforço:** M
**Dependências:** UI-02, CAT-02

---

### UI-04 — Estilização das Páginas de Produtor e Produto

**Descrição:**
Estilizar: detalhe do produto (`products/show`), perfil público do produtor (`producers/show`),
listagem de produtores (`producers/index`) e o bloco de informações de contato.

**Critérios de aceitação:**
- [ ] Página de detalhe com foto em destaque e tipografia hierárquica
- [ ] Bloco de contato com ícones por canal (pode ser emoji: 📞 📱 ✉️)
- [ ] Perfil do produtor com foto, bio e grid de produtos
- [ ] Cards de produtor na listagem com estilo consistente ao card de produto

**Esforço:** M
**Dependências:** UI-02, CAT-03, CAT-05

---

### UI-05 — Estilização do Dashboard e Formulários

**Descrição:**
Estilizar: dashboard do produtor, tabela de produtos, formulários (setup, edição de perfil,
criação/edição de produto) e páginas de auth do Breeze.

**Critérios de aceitação:**
- [ ] Dashboard com layout de sidebar ou tabs simples
- [ ] Tabela de produtos com linhas zebradas, ações alinhadas
- [ ] Formulários com labels, inputs e mensagens de erro bem espaçados
- [ ] Botões primário (verde) e destrutivo (vermelho) distintos visualmente
- [ ] Pages de auth (login, register) com formulário centralizado e estilo limpo
- [ ] Campo de upload com preview da imagem atual (edição)

**Esforço:** M
**Dependências:** UI-02, PROD-04, AUTH-04

---

### DOC-01 — README de Setup e Execução

**Descrição:**
Escrever o `README.md` do projeto com as seções necessárias para que qualquer pessoa
(incluindo o professor) consiga rodar o projeto em sua máquina.

**Seções obrigatórias:**
1. **Sobre o projeto** — descrição, contexto da disciplina, stack
2. **Pré-requisitos** — Docker, Docker Compose, Git
3. **Instalação e configuração** — passo a passo com comandos
4. **Executando o projeto** — `sail up`, `sail npm run dev`
5. **Acessando a aplicação** — URLs (app, Mailpit)
6. **Estrutura de pastas** — resumo dos diretórios principais
7. **Documentação adicional** — link para `/docs`

**Comandos que devem estar documentados:**
```bash
# Clonar e instalar
git clone <repo>
cd projeto-extensao-pw2
cp .env.example .env
composer install
./vendor/bin/sail up -d

# Banco de dados
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan storage:link

# Assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

**Critérios de aceitação:**
- [ ] README com todas as seções listadas
- [ ] Comandos copiáveis em blocos de código
- [ ] URLs de acesso claramente documentadas
- [ ] Seção de pré-requisitos com versões mínimas

**Esforço:** P
**Dependências:** todas as sprints anteriores

---

### DOC-02 — Documentação de Rotas

**Descrição:**
Criar `docs/rotas.md` com a tabela completa de rotas da aplicação, incluindo método HTTP,
URI, controller@método, middleware aplicado e descrição da funcionalidade.
Este documento serve como referência rápida durante o desenvolvimento e para o professor.

**Critérios de aceitação:**
- [ ] Todas as rotas tabeladas com método, URI, controller, middlewares
- [ ] Separadas por grupo (público, auth, dashboard)
- [ ] Arquivo linkado no `docs/index.md`

**Esforço:** P
**Dependências:** todas as rotas implementadas

---

### DOC-03 — Comentários de código nas partes não óbvias

**Descrição:**
Revisar o código produzido nas sprints anteriores e adicionar comentários apenas onde
o "porquê" não é óbvio pela leitura do código. Focos principais:

- `EnsureProducerProfileComplete.php` — explicar a lógica de redirect e anti-loop
- `HomeController@index` — explicar o uso de `withQueryString()` na paginação
- `ProductPolicy.php` — documentar a regra de negócio de isolamento entre produtores
- Migrations com FKs de cascade — comentar o comportamento esperado

**Critérios de aceitação:**
- [ ] Comentários adicionados apenas onde necessário (não em todo método)
- [ ] Nenhum comentário redundante que apenas descreve o que o código já diz
- [ ] PHPDoc em métodos públicos dos controllers principais

**Esforço:** P
**Dependências:** todas as sprints anteriores
