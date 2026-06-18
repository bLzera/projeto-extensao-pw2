@props(['producer'])

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
    </div>
</a>
