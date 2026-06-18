<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProducerProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica a existência do relacionamento, não campos individuais,
        // porque qualquer registro em producers já representa um perfil válido.
        // O loop é evitado estruturalmente: as rotas de setup ficam num grupo
        // separado sem este middleware (ver routes/web.php).
        if (! $request->user()->producer) {
            return redirect()->route('producer.setup');
        }

        return $next($request);
    }
}
