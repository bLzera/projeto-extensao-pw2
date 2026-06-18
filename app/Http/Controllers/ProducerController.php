<?php

namespace App\Http\Controllers;

use App\Models\Producer;

class ProducerController extends Controller
{
    public function index()
    {
        $producers = Producer::with('user')->paginate(12);

        return view('producers.index', compact('producers'));
    }

    public function show(Producer $producer)
    {
        $products = $producer->products()
            ->with('category')
            ->where('is_available', true)
            ->paginate(8);

        return view('producers.show', compact('producer', 'products'));
    }
}
