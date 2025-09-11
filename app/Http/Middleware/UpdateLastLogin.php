<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UpdateLastLogin
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Update only if more than X minutes ago (e.g., 1 min)
            if (!$user->last_login || $user->last_login->diffInMinutes(now()) >= 5) {
                $user->forceFill([
                    'last_login' => now()
                ])->save();
            }
        }
        return $next($request);
    }
}
