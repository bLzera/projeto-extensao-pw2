@props(['product'])

<a href="{{ route('products.show', $product) }}" class="product-card">
    <div class="product-card__photo">
        @if ($product->photo)
            <img src="{{ Storage::url($product->photo) }}" alt="{{ $product->name }}">
        @else
            <div class="product-card__photo-placeholder">
                <span>Sem foto</span>
            </div>
        @endif
        <span class="product-card__badge">{{ $product->category->name }}</span>
        @if ($product->is_featured)
            <span class="product-card__featured">⭐ Destaque</span>
        @endif
    </div>

    <div class="product-card__body">
        <h3 class="product-card__name">{{ $product->name }}</h3>
        <p class="product-card__producer">{{ $product->producer->farm_name }}</p>
        <p class="product-card__price">
            R$ {{ number_format($product->price, 2, ',', '.') }}
            <span class="product-card__unit">/ {{ $product->unit }}</span>
        </p>
    </div>
</a>
