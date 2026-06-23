<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $cities = City::whereHas('producers.products', fn($q) => $q->where('is_available', true))
            ->orderBy('name')
            ->get();

        $products = Product::with(['producer.city', 'category'])
            ->where('is_available', true)
            ->when($request->categoria, fn($q, $slug) =>
                $q->whereHas('category', fn($q) => $q->where('slug', $slug))
            )
            ->when($request->city, fn($q, $city) =>
                $q->whereHas('producer.city', fn($q) => $q->where('name', $city))
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

        $data = [
            'products'        => $products,
            'categories'      => $categories,
            'cities'          => $cities,
            'currentCategory' => $request->categoria,
            'currentCity'     => $request->city,
            'busca'           => $request->busca,
            'ordem'           => $request->ordem ?? 'recentes',
            'favoritedIds'    => $favoritedIds,
        ];

        // Requisições AJAX recebem só o catálogo (search + filtros + grid),
        // que o componente Alpine usa para trocar o conteúdo sem refresh.
        if ($request->ajax()) {
            return view('home._catalog', $data);
        }

        return view('home.index', $data);
    }
}
