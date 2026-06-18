@extends('layouts.app')

@section('title', 'Catálogo — ' . config('app.name'))

@section('content')
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
            <p>Nenhum produto encontrado.</p>
            @if ($currentCategory || $busca)
                <a href="{{ route('home') }}">Ver todos os produtos</a>
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
