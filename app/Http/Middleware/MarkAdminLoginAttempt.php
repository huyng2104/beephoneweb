<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MarkAdminLoginAttempt
{
    public function handle(Request $request, Closure $next): Response
    {
        // If user is not authenticated and tries to access /admin, redirect to login immediately
        // and show the popup on the login page.
        if (!Auth::check()) {
            $request->session()->put('admin_login_attempt', true);
            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()
                ->route('admin.login')
                ->with('error', 'Vui lòng đăng nhập để vào admin');
        }

        return $next($request);
    }
}
