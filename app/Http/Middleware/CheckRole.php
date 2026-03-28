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
        $redirectRoute = in_array('admin', $roles, true) ? 'admin.login' : 'login';

        $user = Auth::user();
// <<<<<<< vinh1
//         if (!$user) {
//             return redirect()->route($redirectRoute);
//         }

//         $currentRole = $user->role?->name ?? $user->role_slug ?? null;
//         if (!$currentRole || !in_array($currentRole, $roles, true)) {
//             Auth::logout();
//             $request->session()->invalidate();
//             $request->session()->regenerateToken();

//             return redirect()
//                 ->route($redirectRoute)
//                 ->with('error', 'tk user không được đăng nhập vào admin');
// =======

        // 1. Kiểm tra xem người dùng đã đăng nhập chưa (Chống lỗi sập trang nếu chưa đăng nhập)
        if (!$user) {
            return redirect()->route('login'); // Hoặc abort(401);
        }

        // 2. Chặn tuyệt đối role 'user' không cho vào admin
        if ($user->role?->name === 'user') {
            abort(403, 'Tài khoản thành viên không thể truy cập khu vực Admin!');
// >>>>>>> main
        }

        // 3. Nếu là Admin, Editor, Manager,... (khác 'user') -> Cho đi tiếp
        return $next($request);
    }
}
