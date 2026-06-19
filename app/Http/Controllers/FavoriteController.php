<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Product $product): JsonResponse
    {
        if (! $request->user()->isBuyer()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $result = $request->user()->favorites()->toggle($product->id);
        $favorited = ! empty($result['attached']);

        return response()->json([
            'favorited' => $favorited,
            'count'     => $product->favoritedBy()->count(),
        ]);
    }
}
