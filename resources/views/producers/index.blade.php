@extends('layouts.app')

@section('title', 'Produtores — ' . config('app.name'))

@section('content')
<div class="container">

    <h1 class="page-title">Produtores</h1>

    @if ($producers->isEmpty())
        <div class="empty-state">
            <span class="empty-state__icon">🏡</span>
            <p class="empty-state__title">Nenhum produtor encontrado</p>
            <p class="empty-state__desc">Em breve novos produtores se juntam à feira.</p>
        </div>
    @else
        <div class="producers-grid">
            @foreach ($producers as $producer)
                <x-producer-card :producer="$producer" />
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $producers->links() }}
        </div>
    @endif

</div>
@endsection
