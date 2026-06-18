@extends('layouts.app')

@section('title', config('app.name') . ' — Marketplace de Produtores Locais')

@section('content')
<div class="container">
    <h1>Bem-vindo à {{ config('app.name') }}</h1>
    <p>Conectando produtores locais com consumidores.</p>
    <a href="{{ route('home') }}">Ver catálogo</a>
</div>
@endsection
