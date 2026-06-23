<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProducerController extends Controller
{
    public function index(Request $request)
    {
        $producers = Producer::with(['user', 'city'])
            ->withCount(['products' => fn($q) => $q->where('is_available', true)])
            ->withAvg('activeRatings', 'stars')
            ->when($request->busca, fn($q, $term) =>
                $q->where(function ($q) use ($term) {
                    $q->where('farm_name', 'like', "%{$term}%")
                      ->orWhereHas('city', fn($q) => $q->where('name', 'like', "%{$term}%"));
                })
            )
            ->orderBy('farm_name')
            ->paginate(12)
            ->withQueryString();

        return view('producers.index', [
            'producers' => $producers,
            'busca'     => $request->busca,
        ]);
    }

    public function show(Producer $producer)
    {
        $products = $producer->products()
            ->with('category')
            ->where('is_available', true)
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(8);

        $averageRating = $producer->activeRatings()->avg('stars');
        $ratingsCount  = $producer->activeRatings()->count();

        // A avaliação do próprio comprador é sempre visível para ele,
        // independente de `hidden` — ele não sabe que foi ocultada.
        $existingRating = Auth::check() && Auth::user()->isBuyer()
            ? $producer->ratings()
                ->where('buyer_id', Auth::id())
                ->where('status', 'active')
                ->first()
            : null;

        // Feed público: 5 mais recentes, ativas e não ocultas.
        $feedRatings = $producer->ratings()
            ->where('status', 'active')
            ->where('hidden', false)
            ->with('buyer')
            ->latest()
            ->take(5)
            ->get();

        // "Ver todas" só faz sentido quando há mais avaliações visíveis do que o feed mostra.
        $visibleRatingsCount = $producer->ratings()
            ->where('status', 'active')
            ->where('hidden', false)
            ->count();

        $favoritedIds = Auth::check() && Auth::user()->isBuyer()
            ? Auth::user()->favorites()->pluck('product_id')
            : collect();

        return view('producers.show', compact(
            'producer', 'products',
            'averageRating', 'ratingsCount', 'existingRating',
            'feedRatings', 'visibleRatingsCount',
            'favoritedIds'
        ));
    }

    public function ratings(Producer $producer)
    {
        $ratings = $producer->ratings()
            ->where('status', 'active')
            ->where('hidden', false)
            ->with('buyer')
            ->latest()
            ->paginate(10);

        $visibleRatingsCount = $producer->ratings()
            ->where('status', 'active')
            ->where('hidden', false)
            ->count();

        $averageRating = $producer->activeRatings()->avg('stars');

        return view('producers.ratings', compact(
            'producer', 'ratings', 'visibleRatingsCount', 'averageRating'
        ));
    }
}
