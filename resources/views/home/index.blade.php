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
    <div x-data="catalogFilters()">
        <div
            class="catalog-content"
            x-ref="content"
            :class="{ 'catalog-content--loading': loading }"
            @click="onContentClick($event)"
        >
            @include('home._catalog')
        </div>
    </div>
</div>
@endsection
