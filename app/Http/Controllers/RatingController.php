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

        $existing = Rating::where('buyer_id', $request->user()->id)
            ->where('producer_id', $producer->id)
            ->first();

        if ($existing && $existing->status === 'active') {
            // Edição de avaliação ativa: marca `edited_at`, preserva a curadoria do vendedor.
            $existing->update([
                'stars'     => $request->stars,
                'comment'   => $request->comment,
                'edited_at' => now(),
            ]);
        } elseif ($existing && $existing->status === 'deleted') {
            // Reativação após exclusão: tratada como nova avaliação (nasce visível, sem badge).
            $existing->update([
                'stars'     => $request->stars,
                'comment'   => $request->comment,
                'status'    => 'active',
                'hidden'    => false,
                'edited_at' => null,
            ]);
        } else {
            Rating::create([
                'buyer_id'    => $request->user()->id,
                'producer_id' => $producer->id,
                'stars'       => $request->stars,
                'comment'     => $request->comment,
            ]);
        }

        return redirect()
            ->route('producers.show', $producer)
            ->with('success', 'Avaliação enviada com sucesso!');
    }

    public function destroy(Request $request, Producer $producer): RedirectResponse
    {
        abort_if(! $request->user()->isBuyer(), 403);

        $rating = Rating::where('buyer_id', $request->user()->id)
            ->where('producer_id', $producer->id)
            ->where('status', 'active')
            ->firstOrFail();

        // Soft delete: preserva o registro, mas tira a nota da média e do feed.
        $rating->update(['status' => 'deleted']);

        return redirect()
            ->route('producers.show', $producer)
            ->with('success', 'Avaliação removida.');
    }
}
