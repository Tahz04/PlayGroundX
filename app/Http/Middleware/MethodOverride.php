<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MethodOverride
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra nếu request là POST và có _method field
        if ($request->isMethod('post') && $request->has('_method')) {
            $method = strtoupper($request->input('_method'));
            $request->setMethod($method);
        }

        return $next($request);
    }
}
