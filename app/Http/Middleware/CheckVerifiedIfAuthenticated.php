<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckVerifiedIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Local/dev convenience: don't block browsing the client site by email verification.
        // Keep the middleware in place so production can still enforce verification.
        if (app()->environment(['local', 'testing'])) {
            return $next($request);
        }

        if ($request->user() && !$request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        return $next($request);
    }
}
