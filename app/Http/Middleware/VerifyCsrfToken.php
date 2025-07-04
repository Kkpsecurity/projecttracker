<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // No exclusions - all routes should use CSRF protection
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, \Closure $next)
    {
        // Add debugging for CSRF token validation
        if ($request->isMethod('POST') && $request->is('admin/login')) {
            \Log::info('CSRF Login Debug', [
                'url' => $request->url(),
                'session_token' => $request->session()->token(),
                'input_token' => $request->input('_token'),
                'header_token' => $request->header('X-CSRF-TOKEN'),
                'session_id' => $request->session()->getId(),
                'has_session' => $request->hasSession(),
                'tokens_match' => hash_equals(
                    (string) $request->session()->token(),
                    (string) $request->input('_token')
                ) || hash_equals(
                    (string) $request->session()->token(),
                    (string) $request->header('X-CSRF-TOKEN')
                )
            ]);
        }

        return parent::handle($request, $next);
    }
}
