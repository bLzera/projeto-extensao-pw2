<?php

namespace App\Http\Controllers\Producer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Producer\StoreSetupRequest;
use App\Models\Producer;

class SetupController extends Controller
{
    public function create()
    {
        if (auth()->user()->producer) {
            return redirect()->route('dashboard');
        }

        return view('auth.setup');
    }

    public function store(StoreSetupRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('producers', 'public');
        }

        Producer::create(array_merge($data, [
            'user_id' => auth()->id(),
        ]));

        session()->flash('success', 'Perfil criado com sucesso!');

        return redirect()->route('dashboard');
    }
}
