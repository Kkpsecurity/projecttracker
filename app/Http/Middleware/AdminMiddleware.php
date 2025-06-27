<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // For now, just ensure the user is authenticated
        // In the future, you could add role-based checks here
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Optional: Add additional admin checks here
        // For example: if (!Auth::user()->isAdmin()) { abort(403); }

        return $next($request);
    }
}
