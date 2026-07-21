<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! session('id_clinica')) {
            return redirect()->route('login.show');
        }

        return $next($request);
    }
}
