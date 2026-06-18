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

                @if ($producer->whatsapp)
                    <p class="contact-block__item">
                        📱 <a href="https://wa.me/55{{ preg_replace('/\D/', '', $producer->whatsapp) }}" target="_blank" rel="noopener">
                            WhatsApp: {{ $producer->whatsapp }}
                        </a>
                    </p>
                @endif

                @if ($producer->contact_email)
                    <p class="contact-block__item">
                        ✉️ <a href="mailto:{{ $producer->contact_email }}">{{ $producer->contact_email }}</a>
                    </p>
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
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="pagination-wrapper">
                {{ $products->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
