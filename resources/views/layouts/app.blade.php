<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&display=swap" rel="stylesheet">
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
                    <div class="header-user"
                         x-data="{ open: false }"
                         @click.outside="open = false"
                         @keydown.escape.window="open = false">

                        <button class="header-user__trigger" type="button"
                                @click="open = !open"
                                :aria-expanded="open.toString()"
                                aria-haspopup="true">
                            <span class="header-user__avatar-wrap">
                                @if(auth()->user()->producer?->photo)
                                    <img class="header-user__avatar"
                                         src="{{ Storage::url(auth()->user()->producer->photo) }}"
                                         alt="{{ auth()->user()->producer->farm_name }}">
                                @else
                                    <span class="header-user__avatar header-user__avatar--placeholder" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                        </svg>
                                    </span>
                                @endif
                            </span>
                            <span class="header-user__name">
                                {{ auth()->user()->producer?->farm_name ?? auth()->user()->name }}
                            </span>
                        </button>

                        <div class="header-user__dropdown"
                             x-show="open"
                             x-transition:enter="hud-enter"
                             x-transition:enter-start="hud-start"
                             x-transition:enter-end="hud-end"
                             x-transition:leave="hud-leave"
                             x-transition:leave-start="hud-end"
                             x-transition:leave-end="hud-start"
                             @click="open = false">
                            <a class="dropdown-item" href="{{ route('dashboard') }}">Minha Feira</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit">Sair</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="header-user"
                         x-data="{ open: false }"
                         @click.outside="open = false"
                         @keydown.escape.window="open = false">

                        <button class="header-user__trigger" type="button"
                                @click="open = !open"
                                :aria-expanded="open.toString()"
                                aria-haspopup="true">
                            <span class="header-user__avatar-wrap">
                                <span class="header-user__avatar header-user__avatar--placeholder" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                    </svg>
                                </span>
                            </span>
                            <span class="header-user__name">Entrar</span>
                        </button>

                        <div class="header-user__dropdown"
                             x-show="open"
                             x-transition:enter="hud-enter"
                             x-transition:enter-start="hud-start"
                             x-transition:enter-end="hud-end"
                             x-transition:leave="hud-leave"
                             x-transition:leave-start="hud-end"
                             x-transition:leave-end="hud-start"
                             @click="open = false">
                            <a class="dropdown-item" href="{{ route('login') }}">Entrar</a>
                            <a class="dropdown-item" href="{{ route('register') }}">Cadastrar-se</a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    @if (session('error'))
        <div class="flash flash--error">{{ session('error') }}</div>
    @endif

    <main class="site-main">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container">
            <p class="site-footer__brand">{{ config('app.name') }}</p>
            <p class="site-footer__tagline">Conectando produtores e consumidores locais.</p>
            <p class="site-footer__meta">Projeto de Extensão — Programação Web II · Unidavi</p>
        </div>
    </footer>
</body>
</html>
