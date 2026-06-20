<?php

namespace App\Http\Controllers\Producer;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRatingController extends Controller
{
    public function toggle(Request $request, Rating $rating): RedirectResponse
    {
        abort_if($rating->producer->user_id !== $request->user()->id, 403);

        $rating->update(['hidden' => ! $rating->hidden]);

        return back()->with(
            'success',
            $rating->hidden ? 'Avaliação ocultada.' : 'Avaliação exibida.'
        );
    }
}
