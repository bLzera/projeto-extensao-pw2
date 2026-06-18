@extends('layouts.app')

@section('title', $product->name . ' — ' . config('app.name'))

@section('content')
<div class="container">

    <a href="{{ route('home') }}" class="back-link">&larr; Voltar ao catálogo</a>

    <div class="product-detail">
        <div class="product-detail__photo">
            @if ($product->photo)
                <img src="{{ Storage::url($product->photo) }}" alt="{{ $product->name }}">
            @else
                <div class="product-detail__photo-placeholder">
                    <span>Sem foto</span>
                </div>
            @endif
        </div>

        <div class="product-detail__info">
            <span class="product-detail__category">{{ $product->category->name }}</span>
            <h1 class="product-detail__name">{{ $product->name }}</h1>

            <p class="product-detail__price">
                R$ {{ number_format($product->price, 2, ',', '.') }}
                <span class="product-detail__unit">/ {{ $product->unit }}</span>
            </p>

            @if ($product->description)
                <p class="product-detail__description">{{ $product->description }}</p>
            @endif

            {{-- Bloco de contato do produtor --}}
            <div class="contact-block">
                <h2 class="contact-block__title">
                    Contato:
                    <a href="{{ route('producers.show', $product->producer) }}" class="contact-block__producer-link">
                        {{ $product->producer->farm_name }}
                    </a>
                </h2>

                @if ($product->producer->phone)
                    <p class="contact-block__item">
                        📞 <a href="tel:{{ preg_replace('/\D/', '', $product->producer->phone) }}">{{ $product->producer->phone }}</a>
                    </p>
                @endif

                @if ($product->producer->whatsapp)
                    <p class="contact-block__item">
                        📱 <a href="https://wa.me/55{{ preg_replace('/\D/', '', $product->producer->whatsapp) }}" target="_blank" rel="noopener">
                            WhatsApp: {{ $product->producer->whatsapp }}
                        </a>
                    </p>
                @endif

                @if ($product->producer->contact_email)
                    <p class="contact-block__item">
                        ✉️ <a href="mailto:{{ $product->producer->contact_email }}">{{ $product->producer->contact_email }}</a>
                    </p>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
