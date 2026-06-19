<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoritesPageController extends Controller
{
    public function index(Request $request): View
    {
        abort_if(! $request->user()->isBuyer(), 403);

        $products = $request->user()
            ->favorites()
            ->with(['producer', 'category'])
            ->paginate(12);

        return view('buyer.favorites', compact('products'));
    }
}
