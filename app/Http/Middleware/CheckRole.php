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
     * Hỗ trợ dùng middleware theo 2 kiểu:
     * - 'role' (không tham số): chặn role 'user' vào Admin
     * - 'role:admin,editor,...': chỉ cho phép các role được liệt kê
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login'); // Hoặc abort(401);
        }

        $roleName = $user->role?->name ?? $user->role ?? null;

        $allowedRoles = [];
        foreach ($roles as $role) {
            if ($role === null || $role === '') {
                continue;
            }

            // Hỗ trợ cú pháp 'admin|editor' nếu có
            $allowedRoles = array_merge(
                $allowedRoles,
                array_values(array_filter(explode('|', (string) $role)))
            );
        }

        if (count($allowedRoles) === 0) {
            if ($roleName === 'user') {
                abort(403, 'Tài khoản thành viên không thể truy cập khu vực Admin!');
            }

            return $next($request);
        }

        if (!$roleName || !in_array($roleName, $allowedRoles, true)) {
            abort(403, 'Bạn không có quyền truy cập!');
        }

        return $next($request);
    }
}
