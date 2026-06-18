<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        if (! $product->is_available) {
            abort(404);
        }

        $product->load(['producer', 'category']);

        return view('products.show', compact('product'));
    }
}
