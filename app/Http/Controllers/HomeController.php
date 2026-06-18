<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::with(['producer', 'category'])
            ->where('is_available', true)
            ->when($request->categoria, fn($q, $slug) =>
                $q->whereHas('category', fn($q) => $q->where('slug', $slug))
            )
            ->when($request->busca, fn($q, $term) =>
                $q->where('name', 'like', "%{$term}%")
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('home.index', [
            'products'        => $products,
            'categories'      => $categories,
            'currentCategory' => $request->categoria,
            'busca'           => $request->busca,
        ]);
    }
}
