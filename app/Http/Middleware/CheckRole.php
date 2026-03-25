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
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    {
        $redirectRoute = in_array('admin', $roles, true) ? 'admin.login' : 'login';

        $user = Auth::user();
        if (!$user) {
            return redirect()->route($redirectRoute);
        }

        $currentRole = $user->role?->name ?? $user->role_slug ?? null;
        if (!$currentRole || !in_array($currentRole, $roles, true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route($redirectRoute)
                ->with('error', 'tk user không được đăng nhập vào admin');
        }
        return $next($request);
    }
}
