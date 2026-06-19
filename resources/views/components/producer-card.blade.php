@props(['producer', 'averageRating' => null])

<a href="{{ route('producers.show', $producer) }}" class="producer-card">
    <div class="producer-card__photo">
        @if ($producer->photo)
            <img src="{{ Storage::url($producer->photo) }}" alt="{{ $producer->farm_name }}">
        @else
            <div class="producer-card__photo-placeholder">
                <span>{{ mb_substr($producer->farm_name, 0, 1) }}</span>
            </div>
        @endif
    </div>

    <div class="producer-card__body">
        <h3 class="producer-card__name">{{ $producer->farm_name }}</h3>
        @if ($producer->city)
            <p class="producer-card__city">{{ $producer->city }}</p>
        @endif
        @if($averageRating !== null)
            <p class="producer-card__rating">
                <span class="star-display">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="star-display__star {{ $i <= round($averageRating) ? 'star-display__star--filled' : '' }}">★</span>
                    @endfor
                </span>
                <span class="producer-card__rating-value">{{ number_format($averageRating, 1) }}</span>
            </p>
        @endif
    </div>
</a>
