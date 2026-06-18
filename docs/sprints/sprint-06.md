# Sprint 6 — Overhaul de Interface e Design System

**Objetivo:** Transformar a UI de um template SaaS genérico em uma vitrine com identidade
de feira e mercado local. Ao fim desta sprint, a aplicação deve ter tipografia com
personalidade, transições orgânicas, hero section na home, footer com âncora visual e
cards com profundidade quente. Esta sprint precede as demais por ser a fundação visual
que as novas features irão herdar.

**Duração estimada:** um dia a dois dias

---

## Backlog

---

### OV-01 — Tipografia com personalidade

**Descrição:**
Importar a fonte `Fraunces` (Google Fonts, variable, display serifada orgânica) para uso
em títulos e elementos de display. O corpo continua com `system-ui`, mas recebe um
`font-variant-numeric: oldstyle-nums` para tornar os números de preço mais humanistas.
Criar o token `$font-family-display` em `_variables.scss` e aplicá-lo nos elementos
de heading relevantes.

**Elementos que recebem a fonte de display:**
- `h1`, `h2` globais
- `.page-title`
- `.product-detail__name`
- `.producer-profile__name`
- `.hero__headline` (ver OV-05)

**Import no layout:**
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&display=swap" rel="stylesheet">
```

**Critérios de aceitação:**
- [ ] `$font-family-display: 'Fraunces', Georgia, serif` definido em `_variables.scss`
- [ ] Fonte importada no `<head>` de `layouts/app.blade.php` e `layouts/guest.blade.php`
- [ ] Todos os `h1` e `h2` da aplicação renderizando em Fraunces
- [ ] Página de detalhe do produto e perfil do produtor com nome em Fraunces
- [ ] Nenhuma regressão de layout causada pela troca de fonte (verificar larguras)

**Esforço:** P
**Dependências:** nenhuma

---

### OV-02 — Tokens de design expandidos

**Descrição:**
Expandir `_variables.scss` com novos tokens que as demais tarefas desta sprint
(e as sprints seguintes) irão consumir. Nenhuma mudança visual ocorre nesta tarefa
— é preparação de fundação.

**Novos tokens a adicionar:**

```scss
// Superfícies
$color-surface:      #fffef9;  // branco quente para cards
$color-surface-dark: #1c3a24;  // verde escuro para footer

// Sombras com tint verde
$shadow-warm-sm: 0 2px 8px rgba(58, 125, 68, .08);
$shadow-warm-md: 0 8px 24px rgba(58, 125, 68, .14);

// Easing
$ease-out:    cubic-bezier(0.4, 0, 0.2, 1);
$ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);

// Transitions prontas pra uso
$transition-base:   .2s cubic-bezier(0.4, 0, 0.2, 1);
$transition-spring: .3s cubic-bezier(0.34, 1.56, 0.64, 1);

// Border radius expandido
$border-radius-lg: 16px;

// Tipografia display
$font-family-display: 'Fraunces', Georgia, serif;
```

**Critérios de aceitação:**
- [ ] Todos os tokens listados adicionados em `_variables.scss`
- [ ] `npm run build` compila sem warnings
- [ ] Tokens documentados com comentário de seção no arquivo

**Esforço:** P
**Dependências:** OV-01

---

### OV-03 — Header integrado ao fundo bege

**Descrição:**
O header atual tem `background: white` e `border-bottom: 1px solid`, que é a anatomia
padrão de qualquer painel SaaS. A mudança é mínima em código mas significativa em percepção:
o header passa a usar o mesmo fundo bege da página (`$color-bg`), com `backdrop-filter`
para semi-transparência quando sticky, eliminando a sensação de "aplicativo empilhado".

**Mudanças em `_layout.scss`:**
```scss
.site-header {
    background: rgba($color-bg, .92);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid transparent; // remove a borda ou torna-a muito sutil
    // ...restante mantido
}
```

**Mudanças nos links de navegação:**
- Adicionar indicador de hover mais expressivo: underline animado com `::after`
  que expande de `width: 0` para `width: 100%` em `$transition-base`

**Critérios de aceitação:**
- [ ] Header com fundo bege semi-transparente ao rolar
- [ ] Sem `border-bottom` visível em repouso (ou com opacidade ≤ 20%)
- [ ] Nav links com underline animado no hover
- [ ] Logo e links permanecem legíveis sobre o fundo bege
- [ ] Comportamento sticky não apresenta flash de cor ao rolar

**Esforço:** P
**Dependências:** OV-02

---

### OV-04 — Footer com identidade verde escura

**Descrição:**
O footer atual é idêntico ao header (branco com borda), fazendo a página "flutuar" sem
ancoragem. O redesign usa `$color-surface-dark` (#1c3a24) como fundo, com texto em creme
claro. Além da cor, o footer ganha uma tagline que reforça a proposta do sistema.

**Estrutura do novo footer:**
```html
<footer class="site-footer">
  <div class="container">
    <p class="site-footer__brand">Feira Digital</p>
    <p class="site-footer__tagline">Conectando produtores e consumidores locais.</p>
    <p class="site-footer__meta">Projeto de Extensão — Programação Web II · Unidavi</p>
  </div>
</footer>
```

**Estilo:**
```scss
.site-footer {
    background: $color-surface-dark;
    padding: $space-7 0 $space-6;

    &__brand { color: white; font-family: $font-family-display; font-size: $font-size-lg; }
    &__tagline { color: rgba(white, .75); }
    &__meta { color: rgba(white, .45); font-size: $font-size-sm; }
}
```

**Critérios de aceitação:**
- [ ] Footer verde escuro com texto em branco/creme
- [ ] Três linhas: marca, tagline e crédito acadêmico
- [ ] Sem link de "Tailwind CSS" ou outros artefatos do scaffold do Breeze
- [ ] Responsivo: texto centralizado em mobile

**Esforço:** P
**Dependências:** OV-02

---

### OV-05 — Hero section na home

**Descrição:**
A home atualmente começa direto na barra de busca — sem apresentação, sem âncora emocional.
A hero section é um bloco acima do catálogo com headline em Fraunces, subtítulo e um
link para a listagem de produtores. Sem imagem de background: o impacto vem da tipografia.

**Posição no template:** entre o `<div class="container">` e o form de busca em
`resources/views/home/index.blade.php`.

**Markup sugerido:**
```html
<section class="hero">
    <div class="container">
        <h1 class="hero__headline">Da terra direto<br>pra sua mesa.</h1>
        <p class="hero__sub">Produtos frescos de pequenos produtores da sua região,
            sem intermediários.</p>
        <a class="hero__link" href="{{ route('producers.index') }}">
            Conhecer os produtores →
        </a>
    </div>
</section>
```

**Estilo em `_catalog.scss`:**
```scss
.hero {
    padding: $space-8 0 $space-6;

    &__headline {
        font-family: $font-family-display;
        font-size: clamp(2.5rem, 6vw, 4rem);
        font-weight: 700;
        line-height: 1.1;
        color: $color-text;
        margin: 0 0 $space-4;
    }

    &__sub {
        font-size: $font-size-lg;
        color: $color-text-muted;
        max-width: 480px;
        margin: 0 0 $space-5;
        line-height: 1.6;
    }

    &__link {
        font-weight: 600;
        color: $color-primary;
        border-bottom: 2px solid $color-primary;
        padding-bottom: 2px;
        transition: opacity $transition-base;

        &:hover { opacity: .75; text-decoration: none; }
    }
}
```

**Critérios de aceitação:**
- [ ] Hero visível acima do campo de busca na home
- [ ] Headline em Fraunces com `clamp()` responsivo (não quebra em mobile)
- [ ] Link "Conhecer os produtores" funciona e direciona para `/produtores`
- [ ] Em mobile (< 576px) a hero tem padding reduzido e cabe sem scroll forçado

**Esforço:** P
**Dependências:** OV-01, OV-02

---

### OV-06 — Transitions com curvas orgânicas

**Descrição:**
Substituir todas as transitions `linear` e implícitas por curvas de easing expressivas
usando os tokens `$transition-base` e `$transition-spring` criados em OV-02.
Adicionar um fade-in suave ao conteúdo principal da página para suavizar a navegação.

**Mudanças por arquivo:**

`_catalog.scss`:
- `.category-nav__item`: `transition: border-color $transition-base, color $transition-base, background $transition-base`
- `.product-card`: `transition: box-shadow $transition-spring, transform $transition-spring`
- `.producer-card`: idem
- `.product-card:hover img`: `transition: transform $transition-spring`

`_dashboard.scss` / `_public.scss`:
- Todos os `.btn`: `transition: background $transition-base, box-shadow $transition-base, transform $transition-base`
- `.btn--primary:hover`: adicionar `box-shadow: 0 4px 12px rgba($color-primary, .3)`
- `.btn:active`: `transform: scale(.97)`

`_base.scss`:
```scss
@keyframes page-fade-in {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.site-main {
    animation: page-fade-in .25s $ease-out both;
}
```

**Critérios de aceitação:**
- [ ] Nenhum `transition` sem curva de easing explícita nos arquivos SCSS
- [ ] Cards de produto e produtor com spring suave no hover
- [ ] Botões com feedback de `active` (leve scale)
- [ ] Conteúdo principal fade-in ao navegar entre páginas
- [ ] `npm run build` sem warnings

**Esforço:** M
**Dependências:** OV-02

---

### OV-07 — Cards com warm borders e profundidade

**Descrição:**
Os cards atuais são `background: white` com `box-shadow` flat. A mudança usa
`$color-surface` (#fffef9, branco quente) como fundo, troca a sombra por uma versão
com tint verde, e aumenta o `border-radius` para `$border-radius-lg`.
O efeito de hover ganha profundidade real em vez de apenas levantar.

**Mudanças no `.product-card`:**
```scss
.product-card {
    background: $color-surface;
    border: 1px solid $color-border;
    border-radius: $border-radius-lg;
    box-shadow: $shadow-warm-sm;

    &:hover {
        box-shadow: $shadow-warm-md;
        border-color: rgba($color-primary, .2);
        transform: translateY(-3px);
    }
}
```

**Aplicar o mesmo padrão em:**
- `.producer-card`
- `.stat-card` (dashboard)
- `.form-card`
- `.contact-block`

**Critérios de aceitação:**
- [ ] Todos os cards com `$color-surface` em vez de `white`
- [ ] `border-radius` aumentado para `$border-radius-lg` (16px)
- [ ] Sombra com tint verde em repouso e hover
- [ ] Borda sutil que escurece ao hover
- [ ] Sem regressão de layout nos breakpoints móbile

**Esforço:** P
**Dependências:** OV-02

---

### OV-08 — Product card v2 com badge de categoria

**Descrição:**
O card de produto atual coloca a categoria como texto abaixo do nome, com tipografia
pequena maiúscula. O redesign move o badge de categoria para dentro da foto
(sobreposição absoluta, canto inferior esquerdo), liberando o corpo do card para
hierarquia mais limpa: nome → produtor → preço.

**Mudanças no componente `product-card.blade.php`:**
```html
<div class="product-card__photo">
    {{-- foto ou placeholder --}}
    <span class="product-card__badge">{{ $product->category->name }}</span>
</div>
<div class="product-card__body">
    <p class="product-card__name">{{ $product->name }}</p>
    <p class="product-card__producer">{{ $product->producer->farm_name }}</p>
    <p class="product-card__price">
        R$ {{ number_format($product->price, 2, ',', '.') }}
        <span class="product-card__unit">/ {{ $product->unit }}</span>
    </p>
</div>
```

**Estilo do badge:**
```scss
.product-card__badge {
    position: absolute;
    bottom: $space-2;
    left: $space-2;
    background: rgba(255, 255, 255, .88);
    backdrop-filter: blur(4px);
    border-radius: 99px;
    padding: 2px $space-3;
    font-size: 0.7rem;
    font-weight: 700;
    color: $color-text;
    letter-spacing: 0.04em;
}

.product-card__photo {
    position: relative; // necessário para o badge absoluto
}
```

**Critérios de aceitação:**
- [ ] Badge de categoria sobreposto na foto, canto inferior esquerdo
- [ ] Badge com `backdrop-filter: blur` legível sobre qualquer cor de imagem
- [ ] Corpo do card sem a linha de categoria maiúscula antiga
- [ ] Preço com unidade inline (`R$ 12,00 / kg`)
- [ ] Card mantém `aspect-ratio: 4/3` na foto sem distorção

**Esforço:** P
**Dependências:** OV-07

---

### OV-09 — Empty states com caráter

**Descrição:**
Os empty states atuais são uma linha de texto simples. O redesign usa um ícone/emoji
grande, um título descritivo e uma linha de suporte com call-to-action quando aplicável.
A estrutura é a mesma para catálogo, listagem de produtores e dashboard de produtos.

**Estrutura padrão:**
```html
<div class="empty-state">
    <span class="empty-state__icon">🌱</span>
    <p class="empty-state__title">Nenhum produto encontrado</p>
    <p class="empty-state__desc">Tente outros termos ou <a href="...">limpe os filtros</a>.</p>
</div>
```

**Variações:**
- Catálogo sem resultado de busca: ícone 🔍, "Nenhum resultado para «{busca}»"
- Catálogo sem produtos (primeira visita): ícone 🌿, "Em breve mais produtos por aqui"
- Dashboard sem produtos: ícone 📦, "Você ainda não tem produtos cadastrados", CTA "Adicionar produto"
- Listagem de produtores vazia: ícone 🏡, "Nenhum produtor encontrado"

**Estilo:**
```scss
.empty-state {
    padding: $space-8 $space-5;
    text-align: center;

    &__icon { font-size: 3rem; display: block; margin-bottom: $space-4; }
    &__title { font-family: $font-family-display; font-size: $font-size-xl;
               color: $color-text; margin: 0 0 $space-2; }
    &__desc  { color: $color-text-muted; margin: 0; }
}
```

**Critérios de aceitação:**
- [ ] Empty state com ícone + título + descrição em todas as listagens
- [ ] Variações corretas para busca vs. lista vazia vs. dashboard
- [ ] CTAs contextuais funcionais (links corretos)
- [ ] Responsivo e sem overflow em mobile

**Esforço:** P
**Dependências:** OV-01, OV-02

---

### OV-10 — Microdetails de formulário

**Descrição:**
Unificar e enriquecer o comportamento visual dos formulários em toda a aplicação.
Os inputs do dashboard (`_dashboard.scss`) não têm a mesma qualidade dos inputs de auth
(`_auth.scss`). Esta tarefa alinha tudo e adiciona microinterações que dão sensação
de polimento.

**Mudanças em `_dashboard.scss`:**
```scss
.form-input,
.form-textarea,
.form-select {
    background: $color-bg;
    transition: border-color $transition-base,
                box-shadow $transition-base,
                background $transition-base;

    &:focus {
        background: white;
        border-color: $color-primary;
        box-shadow: 0 0 0 3px rgba($color-primary, .15);
    }
}
```

**Botões — novo comportamento de hover:**
```scss
.btn--primary {
    &:hover {
        background: color.adjust($color-primary, $lightness: -8%);
        box-shadow: 0 4px 12px rgba($color-primary, .3);
        transform: translateY(-1px);
    }
    &:active { transform: translateY(0); box-shadow: none; }
}
```

**Campo de upload de arquivo — estilização:**
```scss
.file-upload {
    border: 2px dashed $color-border;
    border-radius: $border-radius;
    padding: $space-5;
    text-align: center;
    color: $color-text-muted;
    font-size: $font-size-sm;
    cursor: pointer;
    transition: border-color $transition-base;

    &:hover { border-color: $color-primary; color: $color-primary; }
}
```

**Critérios de aceitação:**
- [ ] Todos os inputs do dashboard com `background: $color-bg` → `white` no focus
- [ ] Focus ring consistente (`box-shadow: 0 0 0 3px`) em toda a aplicação
- [ ] Botão primário com leve elevação (transform + shadow) no hover
- [ ] Campo de upload com estilo dashed e hover expressivo
- [ ] Nenhuma diferença visual entre formulários de auth e dashboard

**Esforço:** P
**Dependências:** OV-02, OV-06
