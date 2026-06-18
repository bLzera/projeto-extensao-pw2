<?php

namespace App\Http\Controllers\Producer;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $producer = auth()->user()->producer()->with('products')->first();

        $totalProducts = $producer->products->count();
        $availableProducts = $producer->products->where('is_available', true)->count();

        return view('dashboard.index', compact('producer', 'totalProducts', 'availableProducts'));
    }
}
