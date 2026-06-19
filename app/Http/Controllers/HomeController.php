<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Producer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $cities = Producer::whereHas('products', fn($q) => $q->where('is_available', true))
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $products = Product::with(['producer', 'category'])
            ->where('is_available', true)
            ->when($request->categoria, fn($q, $slug) =>
                $q->whereHas('category', fn($q) => $q->where('slug', $slug))
            )
            ->when($request->cidade, fn($q, $city) =>
                $q->whereHas('producer', fn($q) => $q->where('city', $city))
            )
            ->when($request->busca, fn($q, $term) =>
                $q->where('name', 'like', "%{$term}%")
            )
            ->when($request->ordem, function ($q, $ordem) {
                match ($ordem) {
                    'menor-preco' => $q->orderBy('price', 'asc'),
                    'maior-preco' => $q->orderBy('price', 'desc'),
                    'az'          => $q->orderBy('name', 'asc'),
                    default       => $q->latest(),
                };
            }, fn($q) => $q->latest())
            ->paginate(12)
            ->withQueryString();

        $favoritedIds = Auth::check() && Auth::user()->isBuyer()
            ? Auth::user()->favorites()->pluck('product_id')
            : collect();

        return view('home.index', [
            'products'        => $products,
            'categories'      => $categories,
            'cities'          => $cities,
            'currentCategory' => $request->categoria,
            'currentCity'     => $request->cidade,
            'busca'           => $request->busca,
            'ordem'           => $request->ordem ?? 'recentes',
            'favoritedIds'    => $favoritedIds,
        ]);
    }
}
