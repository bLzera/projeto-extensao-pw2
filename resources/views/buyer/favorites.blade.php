@extends('layouts.app')
@section('title', 'Meus Favoritos — ' . config('app.name'))

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Meus Favoritos</h1>
    </div>

    @if ($products->isEmpty())
        <div class="empty-state">
            <span class="empty-state__icon">♡</span>
            <p class="empty-state__title">Nenhum favorito ainda</p>
            <p class="empty-state__desc">
                Explore o <a href="{{ route('home') }}">catálogo</a> e salve os produtos que você mais gosta.
            </p>
        </div>
    @else
        <div class="products-grid">
            @foreach ($products as $product)
                <x-product-card :product="$product" :favorited="true" />
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
