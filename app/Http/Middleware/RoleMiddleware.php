<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Role kontrolü kaldırıldı - tek kullanıcı için gerekli değil
        // Sadece authentication kontrolü yapılıyor
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }

    /**
     * Get user role based on user properties
     */
    private function getUserRole($user): string
    {
        // For now, use a simple role system based on email
        // In production, this should check the permissions table
        if (strpos($user->email, 'admin') !== false || strpos($user->email, 'manager') !== false) {
            return 'admin';
        }

        return 'employee';
    }
}