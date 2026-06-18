<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a href="{{ route('home') }}" class="site-logo">{{ config('app.name') }}</a>

            <nav class="site-nav">
                <a href="{{ route('home') }}">Catálogo</a>
                <a href="{{ route('producers.index') }}">Produtores</a>
            </nav>

            <div class="site-auth">
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit">Sair</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Entrar</a>
                @endauth
            </div>
        </div>
    </header>

    @if (session('success'))
        <div class="flash flash--success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="flash flash--error">{{ session('error') }}</div>
    @endif

    <main class="site-main">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>{{ config('app.name') }} &mdash; Projeto de Extensão &bull; Programação Web II &bull; Unidavi</p>
        </div>
    </footer>
</body>
</html>
