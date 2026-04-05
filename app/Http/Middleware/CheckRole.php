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
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Kiểm tra xem người dùng đã đăng nhập chưa.
        if (! $user) {
            return redirect()->route('login');
        }

        $roleValue = $user->role;
        $roleName = is_object($roleValue)
            ? ($roleValue->name ?? $roleValue->name_role ?? null)
            : $roleValue;

        // 2. Chặn tuyệt đối role user không cho vào admin.
        if ($roleName === 'user') {
            abort(403, 'Tài khoản thành viên không thể truy cập khu vực Admin!');
        }

        // 3. Nếu là admin/staff/... thì cho đi tiếp.
        return $next($request);
    }
}