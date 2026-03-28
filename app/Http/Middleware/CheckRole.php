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

        // 1. Kiểm tra xem người dùng đã đăng nhập chưa (Chống lỗi sập trang nếu chưa đăng nhập)
        if (!$user) {
            return redirect()->route('login'); // Hoặc abort(401);
        }

        // 2. Chặn tuyệt đối role 'user' không cho vào admin
        if ($user->role?->name === 'user') {
            abort(403, 'Tài khoản thành viên không thể truy cập khu vực Admin!');
        }

        // 3. Nếu là Admin, Editor, Manager,... (khác 'user') -> Cho đi tiếp
        return $next($request);
    }
}
