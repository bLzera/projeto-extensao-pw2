<?php

namespace App\Http\Controllers\Producer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Producer\UpdateProfileRequest;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $producer = auth()->user()->producer;

        return view('dashboard.profile.edit', compact('producer'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $producer = auth()->user()->producer;
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($producer->photo) {
                Storage::disk('public')->delete($producer->photo);
            }
            $data['photo'] = $request->file('photo')->store('producers', 'public');
        } else {
            unset($data['photo']);
        }

        $producer->update($data);

        return redirect()->route('producer.profile.edit')
            ->with('success', 'Perfil atualizado com sucesso!');
    }
}
