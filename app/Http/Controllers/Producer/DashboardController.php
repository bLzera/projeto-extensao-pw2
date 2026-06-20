<?php

namespace App\Http\Controllers\Producer;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $producer = auth()->user()->producer;

        $products = $producer->products()
            ->with('category')
            ->latest()
            ->paginate(10);

        $totalProducts = $producer->products()->count();
        $availableProducts = $producer->products()->where('is_available', true)->count();

        // Painel de avaliações: ativas (visíveis e ocultas), com page param próprio
        // para não colidir com a paginação de produtos.
        $ratings = $producer->ratings()
            ->where('status', 'active')
            ->with('buyer')
            ->latest()
            ->paginate(10, ['*'], 'avaliacoes');

        $activeRatingsCount = $producer->activeRatings()->count();
        $hiddenRatingsCount = $producer->ratings()
            ->where('status', 'active')
            ->where('hidden', true)
            ->count();
        $averageRating = $producer->activeRatings()->avg('stars');

        return view('dashboard.index', compact(
            'producer', 'products', 'totalProducts', 'availableProducts',
            'ratings', 'activeRatingsCount', 'hiddenRatingsCount', 'averageRating'
        ));
    }
}
