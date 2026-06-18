<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProducerProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->producer) {
            return redirect()->route('producer.setup');
        }

        return $next($request);
    }
}
