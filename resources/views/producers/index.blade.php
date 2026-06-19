@extends('layouts.app')

@section('title', 'Produtores — ' . config('app.name'))

@section('content')
<div class="container">

    <h1 class="page-title">Produtores</h1>

    {{-- Barra de busca --}}
    <form class="search-bar" method="GET" action="{{ route('producers.index') }}">
        <input
            type="search"
            name="busca"
            value="{{ $busca }}"
            placeholder="Buscar por nome ou cidade..."
            class="search-bar__input"
        >
        <button type="submit" class="search-bar__btn">Buscar</button>
    </form>

    {{-- Contagem de resultados --}}
    <p class="results-count">
        {{ $producers->total() }}
        {{ Str::plural('produtor', $producers->total()) }}
        @if ($busca)
            {{ $producers->total() === 1 ? 'encontrado' : 'encontrados' }} para &ldquo;{{ $busca }}&rdquo;
        @endif
    </p>

    @if ($producers->isEmpty())
        <div class="empty-state">
            <span class="empty-state__icon">🏡</span>
            @if ($busca)
                <p class="empty-state__title">Nenhum produtor encontrado</p>
                <p class="empty-state__desc">Tente outro nome ou cidade, ou <a href="{{ route('producers.index') }}">veja todos os produtores</a>.</p>
            @else
                <p class="empty-state__title">Nenhum produtor encontrado</p>
                <p class="empty-state__desc">Em breve novos produtores se juntam à feira.</p>
            @endif
        </div>
    @else
        <div class="producers-grid">
            @foreach ($producers as $producer)
                <x-producer-card :producer="$producer" :averageRating="$producer->ratings_avg_stars" />
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $producers->links() }}
        </div>
    @endif

</div>
@endsection
