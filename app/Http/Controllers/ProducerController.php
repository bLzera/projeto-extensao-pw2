<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProducerController extends Controller
{
    public function index(Request $request)
    {
        $producers = Producer::with('user')
            ->withCount(['products' => fn($q) => $q->where('is_available', true)])
            ->withAvg('ratings', 'stars')
            ->when($request->busca, fn($q, $term) =>
                $q->where(function ($q) use ($term) {
                    $q->where('farm_name', 'like', "%{$term}%")
                      ->orWhere('city', 'like', "%{$term}%");
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

        $averageRating  = $producer->ratings()->avg('stars');
        $ratingsCount   = $producer->ratings()->count();
        $existingRating = Auth::check() && Auth::user()->isBuyer()
            ? $producer->ratings()->where('buyer_id', Auth::id())->first()
            : null;
        $favoritedIds = Auth::check() && Auth::user()->isBuyer()
            ? Auth::user()->favorites()->pluck('product_id')
            : collect();

        return view('producers.show', compact(
            'producer', 'products',
            'averageRating', 'ratingsCount', 'existingRating',
            'favoritedIds'
        ));
    }
}
