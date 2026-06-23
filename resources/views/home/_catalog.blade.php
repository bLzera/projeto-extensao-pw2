@php
    // Parâmetros que devem persistir entre os filtros (ordem só quando não for o padrão).
    $ordemParam = ($ordem && $ordem !== 'recentes') ? ['ordem' => $ordem] : [];

    $currentCategoryName = $currentCategory
        ? $categories->firstWhere('slug', $currentCategory)?->name
        : null;

    $ordemLabels = [
        'recentes'    => 'Mais recentes',
        'menor-preco' => 'Menor preço',
        'maior-preco' => 'Maior preço',
        'az'          => 'Nome (A–Z)',
    ];
@endphp

{{-- Barra de busca (GET tradicional, com refresh) --}}
<form class="search-bar" method="GET" action="{{ route('home') }}">
    @if ($currentCategory)
        <input type="hidden" name="categoria" value="{{ $currentCategory }}">
    @endif
    @if ($currentCity)
        <input type="hidden" name="city" value="{{ $currentCity }}">
    @endif
    @if ($ordem && $ordem !== 'recentes')
        <input type="hidden" name="ordem" value="{{ $ordem }}">
    @endif
    <input
        type="search"
        name="busca"
        value="{{ $busca }}"
        placeholder="Buscar produtos..."
        class="search-bar__input"
    >
    <button type="submit" class="search-bar__btn">Buscar</button>
</form>

{{-- Barra de filtros (dropdowns por dimensão) --}}
<div class="filter-bar">

    {{-- Categoria --}}
    <div class="filter-dropdown">
        <button
            type="button"
            class="filter-dropdown__trigger"
            @click="openMenu = (openMenu === 'categoria' ? null : 'categoria')"
            :class="{ 'filter-dropdown__trigger--open': openMenu === 'categoria' }"
            :aria-expanded="(openMenu === 'categoria').toString()"
        >
            <span class="filter-dropdown__label">Categoria</span>
            <span class="filter-dropdown__value">{{ $currentCategoryName ?? 'Todas' }}</span>
            <span class="filter-dropdown__caret" aria-hidden="true">▾</span>
        </button>

        <ul
            class="filter-dropdown__menu"
            x-show="openMenu === 'categoria'"
            @click.outside="openMenu = null"
            x-transition.origin.top.left
            x-cloak
        >
            <li>
                <a
                    href="{{ route('home', array_filter(['busca' => $busca, 'city' => $currentCity] + $ordemParam)) }}"
                    class="filter-dropdown__option {{ ! $currentCategory ? 'filter-dropdown__option--active' : '' }}"
                    @click.prevent="apply($el.getAttribute('href'))"
                >Todas</a>
            </li>
            @foreach ($categories as $category)
                <li>
                    <a
                        href="{{ route('home', array_filter(['categoria' => $category->slug, 'busca' => $busca, 'city' => $currentCity] + $ordemParam)) }}"
                        class="filter-dropdown__option {{ $currentCategory === $category->slug ? 'filter-dropdown__option--active' : '' }}"
                        @click.prevent="apply($el.getAttribute('href'))"
                    >{{ $category->name }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Cidade (só quando houver mais de uma) --}}
    @if ($cities->count() > 1)
        <div class="filter-dropdown">
            <button
                type="button"
                class="filter-dropdown__trigger"
                @click="openMenu = (openMenu === 'cidade' ? null : 'cidade')"
                :class="{ 'filter-dropdown__trigger--open': openMenu === 'cidade' }"
                :aria-expanded="(openMenu === 'cidade').toString()"
            >
                <span class="filter-dropdown__label">Cidade</span>
                <span class="filter-dropdown__value">{{ $currentCity ?? 'Todas' }}</span>
                <span class="filter-dropdown__caret" aria-hidden="true">▾</span>
            </button>

            <ul
                class="filter-dropdown__menu"
                x-show="openMenu === 'cidade'"
                @click.outside="openMenu = null"
                x-transition.origin.top.left
                x-cloak
            >
                <li>
                    <a
                        href="{{ route('home', array_filter(['busca' => $busca, 'categoria' => $currentCategory] + $ordemParam)) }}"
                        class="filter-dropdown__option {{ ! $currentCity ? 'filter-dropdown__option--active' : '' }}"
                        @click.prevent="apply($el.getAttribute('href'))"
                    >Todas</a>
                </li>
                @foreach ($cities as $city)
                    <li>
                        <a
                            href="{{ route('home', array_filter(['city' => $city->name, 'busca' => $busca, 'categoria' => $currentCategory] + $ordemParam)) }}"
                            class="filter-dropdown__option {{ $currentCity === $city->name ? 'filter-dropdown__option--active' : '' }}"
                            @click.prevent="apply($el.getAttribute('href'))"
                        >{{ $city->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Ordenação --}}
    <div class="filter-dropdown filter-dropdown--end">
        <button
            type="button"
            class="filter-dropdown__trigger"
            @click="openMenu = (openMenu === 'ordem' ? null : 'ordem')"
            :class="{ 'filter-dropdown__trigger--open': openMenu === 'ordem' }"
            :aria-expanded="(openMenu === 'ordem').toString()"
        >
            <span class="filter-dropdown__label">Ordenar por</span>
            <span class="filter-dropdown__value">{{ $ordemLabels[$ordem] ?? $ordemLabels['recentes'] }}</span>
            <span class="filter-dropdown__caret" aria-hidden="true">▾</span>
        </button>

        <ul
            class="filter-dropdown__menu filter-dropdown__menu--right"
            x-show="openMenu === 'ordem'"
            @click.outside="openMenu = null"
            x-transition.origin.top.right
            x-cloak
        >
            @foreach ($ordemLabels as $value => $label)
                <li>
                    <a
                        href="{{ route('home', array_filter([
                            'ordem'     => $value === 'recentes' ? null : $value,
                            'categoria' => $currentCategory,
                            'city'      => $currentCity,
                            'busca'     => $busca,
                        ])) }}"
                        class="filter-dropdown__option {{ $ordem === $value ? 'filter-dropdown__option--active' : '' }}"
                        @click.prevent="apply($el.getAttribute('href'))"
                    >{{ $label }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

{{-- Filtros ativos --}}
@if ($currentCategory || $currentCity || $busca)
    <div class="filters-active">
        <span>Filtrando por:
            @if ($currentCategory)
                <strong>{{ $currentCategoryName }}</strong>
            @endif
            @if ($currentCity)
                <strong>{{ $currentCity }}</strong>
            @endif
            @if ($busca)
                &ldquo;{{ $busca }}&rdquo;
            @endif
        </span>
        <a
            href="{{ route('home') }}"
            class="filters-active__clear"
            @click.prevent="apply($el.getAttribute('href'))"
        >Limpar filtros</a>
    </div>
@endif

{{-- Grid de produtos --}}
@if ($products->isEmpty())
    <div class="empty-state">
        @if ($currentCategory || $currentCity || $busca)
            <span class="empty-state__icon">🔍</span>
            <p class="empty-state__title">Nenhum resultado encontrado</p>
            <p class="empty-state__desc">Tente outros termos ou <a href="{{ route('home') }}" @click.prevent="apply($el.getAttribute('href'))">limpe os filtros</a>.</p>
        @else
            <span class="empty-state__icon">🌿</span>
            <p class="empty-state__title">Em breve mais produtos por aqui</p>
            <p class="empty-state__desc">Estamos crescendo. Volte em breve!</p>
        @endif
    </div>
@else
    <div class="products-grid">
        @foreach ($products as $product)
            <x-product-card :product="$product" :favorited="$favoritedIds->contains($product->id)" />
        @endforeach
    </div>

    <div class="pagination-wrapper">
        {{ $products->links() }}
    </div>
@endif
