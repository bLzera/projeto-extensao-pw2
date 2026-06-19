@props(['product', 'favorited' => false])

<div class="product-card" x-data="{ favorited: {{ $favorited ? 'true' : 'false' }} }">
    <a href="{{ route('products.show', $product) }}" class="product-card__link">
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

    @auth
        @if(auth()->user()->isBuyer())
        <button
            type="button"
            class="product-card__heart"
            :class="{ 'product-card__heart--active': favorited }"
            @click="
                fetch('{{ route('favorites.toggle', $product) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    }
                }).then(r => r.json()).then(data => { favorited = data.favorited; })
            "
            :aria-label="favorited ? 'Remover dos favoritos' : 'Adicionar aos favoritos'">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                 :fill="favorited ? 'currentColor' : 'none'"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </button>
        @endif
    @endauth
</div>
