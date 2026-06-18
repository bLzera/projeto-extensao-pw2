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

<div class="container">

    {{-- Barra de busca --}}
    <form class="search-bar" method="GET" action="{{ route('home') }}">
        @if ($currentCategory)
            <input type="hidden" name="categoria" value="{{ $currentCategory }}">
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

    {{-- Menu de categorias --}}
    <nav class="category-nav">
        <a
            href="{{ route('home', $busca ? ['busca' => $busca] : []) }}"
            class="category-nav__item {{ ! $currentCategory ? 'category-nav__item--active' : '' }}"
        >Todos</a>

        @foreach ($categories as $category)
            <a
                href="{{ route('home', array_filter(['categoria' => $category->slug, 'busca' => $busca])) }}"
                class="category-nav__item {{ $currentCategory === $category->slug ? 'category-nav__item--active' : '' }}"
            >{{ $category->name }}</a>
        @endforeach
    </nav>

    {{-- Filtros ativos --}}
    @if ($currentCategory || $busca)
        <div class="filters-active">
            <span>Filtrando por:
                @if ($currentCategory)
                    <strong>{{ $categories->firstWhere('slug', $currentCategory)?->name }}</strong>
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
            @if ($currentCategory || $busca)
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
