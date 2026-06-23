<?php

namespace App\Http\Controllers\Producer;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRatingController extends Controller
{
    public function toggleAll(Request $request): JsonResponse|RedirectResponse
    {
        $producer = $request->user()->producer;
        abort_unless($producer, 403);

        $hidden = $request->boolean('hidden');

        $producer->ratings()->update(['hidden' => $hidden]);

        if($request->expectsJson()){
            return response()->json([
                'hidden' => $hidden,
                'count' => $producer->ratings()
                    ->where('hidden', true)
                    ->count(),
                'message' => $hidden ? 'Todas as avaliações foram ocultadas.' : 'Todas as avaliações foram exibidas',
            ]);
        }

        return back()->with('success', $hidden ? 'Todas as avaliações foram ocultadas.' : 'Todas as avaliações foram exibidas');
    }   

    public function toggle(Request $request, Rating $rating): JsonResponse|RedirectResponse
    {
        abort_if($rating->producer->user_id !== $request->user()->id, 403);

        $rating->update(['hidden' => ! $rating->hidden]);

        if($request->expectsJson()){
            return response()->json([
                'hidden' => $rating->hidden,
                'count' => $rating->producer->ratings()
                    ->where('hidden', true)
                    ->count(),
                'message' => $rating->hidden ? 'Avaliação ocultada' : 'Avaliação exibida',
            ]);
        }

        return back()->with(
            'success',
            $rating->hidden ? 'Avaliação ocultada.' : 'Avaliação exibida.'
        );
    }
}
