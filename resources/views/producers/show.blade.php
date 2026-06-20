@extends('layouts.app')

@section('title', $producer->farm_name . ' — ' . config('app.name'))

@section('content')
<div class="container">

    <a href="{{ route('producers.index') }}" class="back-link">&larr; Voltar aos produtores</a>

    {{-- Perfil do produtor --}}
    <div class="producer-profile">
        <div class="producer-profile__photo">
            @if ($producer->photo)
                <img src="{{ Storage::url($producer->photo) }}" alt="{{ $producer->farm_name }}">
            @else
                <div class="producer-profile__photo-placeholder">
                    <span>{{ mb_substr($producer->farm_name, 0, 1) }}</span>
                </div>
            @endif
        </div>

        <div class="producer-profile__info">
            <h1 class="producer-profile__name">{{ $producer->farm_name }}</h1>

            @if ($producer->city)
                <p class="producer-profile__city">{{ $producer->city }}</p>
            @endif

            @if($averageRating !== null)
                <div class="producer-profile__rating">
                    <span class="star-display">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star-display__star {{ $i <= round($averageRating) ? 'star-display__star--filled' : '' }}">★</span>
                        @endfor
                    </span>
                    <span class="producer-profile__rating-text">
                        {{ number_format($averageRating, 1) }} ({{ $ratingsCount }} {{ $ratingsCount === 1 ? 'avaliação' : 'avaliações' }})
                    </span>
                </div>
            @endif

            @if ($producer->description)
                <p class="producer-profile__description">{{ $producer->description }}</p>
            @endif

            {{-- Canais de contato --}}
            <div class="contact-block">
                @if ($producer->phone)
                    <p class="contact-block__item">
                        📞 <a href="tel:{{ preg_replace('/\D/', '', $producer->phone) }}">{{ $producer->phone }}</a>
                    </p>
                @endif

                @if ($producer->contact_email)
                    <p class="contact-block__item">
                        ✉️ <a href="mailto:{{ $producer->contact_email }}">{{ $producer->contact_email }}</a>
                    </p>
                @endif

                @if ($producer->whatsappUrl())
                    <a class="whatsapp-btn" href="{{ $producer->whatsappUrl() }}" target="_blank" rel="noopener">
                        💬 Falar no WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Produtos do produtor --}}
    <section class="producer-products">
        <h2 class="producer-products__title">Produtos disponíveis</h2>

        @if ($products->isEmpty())
            <div class="empty-state">
                <p>Este produtor ainda não tem produtos disponíveis.</p>
            </div>
        @else
            <div class="products-grid">
                @foreach ($products as $product)
                    <x-product-card :product="$product" :favorited="$favoritedIds->contains($product->id)" />
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $products->links() }}
            </div>
        @endif
    </section>

    @if (session('success'))
        <div class="alert alert--success">{{ session('success') }}</div>
    @endif

    @auth
        @if(auth()->user()->isBuyer())
            <section class="rating-section" x-data="{ editing: {{ $existingRating ? 'false' : 'true' }} }">
                <h2 class="rating-section__title">
                    {{ $existingRating ? 'Sua avaliação' : 'Avaliar este produtor' }}
                </h2>

                {{-- Avaliação já existente: visão de leitura com ações --}}
                @if ($existingRating)
                    <div class="own-rating" x-show="!editing" x-cloak>
                        <div class="own-rating__head">
                            <span class="star-display">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="star-display__star {{ $i <= $existingRating->stars ? 'star-display__star--filled' : '' }}">★</span>
                                @endfor
                            </span>
                            @if ($existingRating->edited_at)
                                <span class="rating-card__edited">editada</span>
                            @endif
                        </div>

                        @if ($existingRating->comment)
                            <p class="own-rating__comment">{{ $existingRating->comment }}</p>
                        @endif

                        <div class="own-rating__actions">
                            <button type="button" class="btn btn--sm btn--outline" @click="editing = true">Editar</button>
                            <form method="POST" action="{{ route('ratings.destroy', $producer) }}"
                                  onsubmit="return confirm('Tem certeza que deseja remover sua avaliação?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn--sm btn--danger" type="submit">Excluir</button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Formulário de criar/editar --}}
                <form method="POST" action="{{ route('ratings.upsert', $producer) }}" class="rating-form"
                      x-show="editing" @if($existingRating) x-cloak @endif
                      x-data="{ stars: {{ old('stars', $existingRating?->stars) ?? 0 }} }">
                    @csrf

                    <div class="rating-form__stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="rating-form__star-label">
                                <input type="radio" name="stars" value="{{ $i }}"
                                       x-model.number="stars"
                                       style="position:absolute;opacity:0;pointer-events:none">
                                <span class="rating-form__star" :class="{ 'rating-form__star--filled': stars >= {{ $i }} }">★</span>
                            </label>
                        @endfor
                        @error('stars')
                            <span class="auth-form__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="auth-form__group">
                        <label class="auth-form__label" for="comment">Comentário (opcional)</label>
                        <textarea class="auth-form__input" id="comment" name="comment"
                                  rows="3" style="resize:vertical">{{ old('comment', $existingRating?->comment) }}</textarea>
                        @error('comment')
                            <span class="auth-form__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="auth-form__footer">
                        <button class="btn btn--primary" type="submit">
                            {{ $existingRating ? 'Atualizar avaliação' : 'Enviar avaliação' }}
                        </button>
                        @if ($existingRating)
                            <button type="button" class="btn btn--outline" @click="editing = false">Cancelar</button>
                        @endif
                    </div>
                </form>
            </section>
        @endif
    @endauth

    {{-- Feed público de avaliações --}}
    @if ($feedRatings->isNotEmpty())
        <section class="ratings-feed">
            <div class="ratings-feed__header">
                <h2 class="rating-section__title">Avaliações</h2>
                <span class="ratings-feed__summary">
                    @if ($averageRating !== null)
                        <strong>{{ number_format($averageRating, 1) }}</strong> &middot;
                    @endif
                    {{ $ratingsCount }} {{ $ratingsCount === 1 ? 'avaliação' : 'avaliações' }}
                </span>
            </div>

            <div class="ratings-feed__list">
                @foreach ($feedRatings as $rating)
                    <x-rating-card :rating="$rating" />
                @endforeach
            </div>

            @if ($visibleRatingsCount > 5)
                <div class="ratings-feed__footer">
                    <a href="{{ route('producers.ratings.index', $producer) }}" class="btn btn--outline">
                        Ver todas as avaliações
                    </a>
                </div>
            @endif
        </section>
    @endif

</div>
@endsection
