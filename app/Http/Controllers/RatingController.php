<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function upsert(Request $request, Producer $producer): RedirectResponse
    {
        abort_if(! $request->user()->isBuyer(), 403);

        $request->validate([
            'stars'   => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Rating::updateOrCreate(
            ['buyer_id' => $request->user()->id, 'producer_id' => $producer->id],
            ['stars' => $request->stars, 'comment' => $request->comment]
        );

        return redirect()
            ->route('producers.show', $producer)
            ->with('success', 'Avaliação enviada com sucesso!');
    }
}
