@extends('layouts.app')

@section('title', 'Avaliações de ' . $producer->farm_name . ' — ' . config('app.name'))

@section('content')
<div class="container">

    <a href="{{ route('producers.show', $producer) }}" class="back-link">&larr; Voltar ao perfil</a>

    <section class="ratings-feed ratings-feed--page">
        <div class="ratings-feed__header">
            <h1 class="rating-section__title">Avaliações de {{ $producer->farm_name }}</h1>
            <span class="ratings-feed__summary">
                @if ($averageRating !== null)
                    <strong>{{ number_format($averageRating, 1) }}</strong> &middot;
                @endif
                {{ $visibleRatingsCount }} {{ $visibleRatingsCount === 1 ? 'avaliação' : 'avaliações' }}
            </span>
        </div>

        @if ($ratings->isEmpty())
            <div class="empty-state">
                <p>Este produtor ainda não tem avaliações.</p>
            </div>
        @else
            <div class="ratings-feed__list">
                @foreach ($ratings as $rating)
                    <x-rating-card :rating="$rating" />
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $ratings->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
