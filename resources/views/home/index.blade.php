@extends('layouts.app')

@section('title', 'Catálogo — ' . config('app.name'))

@section('content')
<section class="hero">
    <div class="container">
        <h1 class="hero__headline">Da terra direto<br>pra sua mesa.</h1>
        <p class="hero__sub">Produtos frescos de pequenos produtores da sua região, sem intermediários.</p>
        <a class="hero__link" href="{{ route('producers.index') }}">Conhecer os produtores →</a>
    </div>
</section>

@php
    // Parâmetros que devem persistir entre os filtros (ordem só quando não for o padrão).
    $ordemParam = ($ordem && $ordem !== 'recentes') ? ['ordem' => $ordem] : [];
@endphp

<div class="container">

    {{-- Barra de busca + ordenação --}}
    <div class="catalog-toolbar">
        <form class="search-bar" method="GET" action="{{ route('home') }}">
            @if ($currentCategory)
                <input type="hidden" name="categoria" value="{{ $currentCategory }}">
            @endif
            @if ($currentCity)
                <input type="hidden" name="cidade" value="{{ $currentCity }}">
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

        <form class="sort-form" method="GET" action="{{ route('home') }}">
            @if ($currentCategory)
                <input type="hidden" name="categoria" value="{{ $currentCategory }}">
            @endif
            @if ($currentCity)
                <input type="hidden" name="cidade" value="{{ $currentCity }}">
            @endif
            @if ($busca)
                <input type="hidden" name="busca" value="{{ $busca }}">
            @endif
            <label for="ordem" class="sort-form__label">Ordenar por</label>
            <select id="ordem" name="ordem" class="sort-form__select" onchange="this.form.submit()">
                <option value="recentes" @selected($ordem === 'recentes')>Mais recentes</option>
                <option value="menor-preco" @selected($ordem === 'menor-preco')>Menor preço</option>
                <option value="maior-preco" @selected($ordem === 'maior-preco')>Maior preço</option>
                <option value="az" @selected($ordem === 'az')>Nome (A–Z)</option>
            </select>
        </form>
    </div>

    {{-- Menu de categorias --}}
    <nav class="category-nav">
        <a
            href="{{ route('home', array_filter(['busca' => $busca, 'cidade' => $currentCity] + $ordemParam)) }}"
            class="category-nav__item {{ ! $currentCategory ? 'category-nav__item--active' : '' }}"
        >Todos</a>

        @foreach ($categories as $category)
            <a
                href="{{ route('home', array_filter(['categoria' => $category->slug, 'busca' => $busca, 'cidade' => $currentCity] + $ordemParam)) }}"
                class="category-nav__item {{ $currentCategory === $category->slug ? 'category-nav__item--active' : '' }}"
            >{{ $category->name }}</a>
        @endforeach
    </nav>

    {{-- Menu de cidades (só quando houver mais de uma) --}}
    @if ($cities->count() > 1)
        <nav class="category-nav">
            <a
                href="{{ route('home', array_filter(['busca' => $busca, 'categoria' => $currentCategory] + $ordemParam)) }}"
                class="category-nav__item {{ ! $currentCity ? 'category-nav__item--active' : '' }}"
            >Todas as cidades</a>

            @foreach ($cities as $city)
                <a
                    href="{{ route('home', array_filter(['cidade' => $city, 'busca' => $busca, 'categoria' => $currentCategory] + $ordemParam)) }}"
                    class="category-nav__item {{ $currentCity === $city ? 'category-nav__item--active' : '' }}"
                >{{ $city }}</a>
            @endforeach
        </nav>
    @endif

    {{-- Filtros ativos --}}
    @if ($currentCategory || $currentCity || $busca)
        <div class="filters-active">
            <span>Filtrando por:
                @if ($currentCategory)
                    <strong>{{ $categories->firstWhere('slug', $currentCategory)?->name }}</strong>
                @endif
                @if ($currentCity)
                    <strong>{{ $currentCity }}</strong>
                @endif
                @if ($busca)
                    &ldquo;{{ $busca }}&rdquo;
                @endif
            </span>
            <a href="{{ route('home') }}" class="filters-active__clear">Limpar filtros</a>
        </div>
    @endif

    {{-- Grid de produtos --}}
    @if ($products->isEmpty())
        <div class="empty-state">
            @if ($currentCategory || $currentCity || $busca)
                <span class="empty-state__icon">🔍</span>
                <p class="empty-state__title">Nenhum resultado encontrado</p>
                <p class="empty-state__desc">Tente outros termos ou <a href="{{ route('home') }}">limpe os filtros</a>.</p>
            @else
                <span class="empty-state__icon">🌿</span>
                <p class="empty-state__title">Em breve mais produtos por aqui</p>
                <p class="empty-state__desc">Estamos crescendo. Volte em breve!</p>
            @endif
        </div>
    @else
        <div class="products-grid">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection
