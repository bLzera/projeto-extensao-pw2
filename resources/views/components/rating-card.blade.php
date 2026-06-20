@props(['rating'])

<article class="rating-card">
    <div class="rating-card__header">
        <div class="rating-card__avatar">
            <span>{{ mb_substr($rating->buyer->name ?? '?', 0, 1) }}</span>
        </div>
        <div class="rating-card__meta">
            <span class="rating-card__author">{{ $rating->buyer->name ?? 'Comprador' }}</span>
            <span class="star-display">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="star-display__star {{ $i <= $rating->stars ? 'star-display__star--filled' : '' }}">★</span>
                @endfor
            </span>
        </div>
        @if ($rating->edited_at)
            <span class="rating-card__edited">editada</span>
        @endif
    </div>

    @if ($rating->comment)
        <p class="rating-card__comment">{{ $rating->comment }}</p>
    @endif
</article>
