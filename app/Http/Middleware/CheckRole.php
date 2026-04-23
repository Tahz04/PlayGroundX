<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Bạn cần đăng nhập trước.');
        }

        $user = Auth::user();
        
        // Ensure role is loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        if (!$user->role) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập vào khu vực này.');
        }

        $allowedRoles = explode('|', $roles);

        if (!in_array($user->role->name, $allowedRoles, true)) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập vào khu vực này.');
        }

        return $next($request);
    }
}
