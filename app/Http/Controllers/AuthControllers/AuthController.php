<?php

namespace App\Http\Controllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreRegister;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        if (!Auth::check()) {
            return view('auth.login.index');
        }
        return redirect()->route('/');
    }
    public function register()
    {
        if (!Auth::check()) {
            return view('auth.register.index');
        }
        return redirect()->route('/');
    }
    public function postLogin(LoginRequest $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password,

        ];
        if (Auth::attempt($data)) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with(
                [
                    'success' => 'Đăng nhập thành công'
                ]
            );
        }
        return back()->withErrors([
            'password' => 'Password không chính xác'
        ])->onlyInput('email');
    }
    public function postRegister(StoreRegister $request)
    {
        $role = Role::where('name', 'user')->first();
        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'status' => 'active'
        ];

        try {
            DB::transaction(function () use ($data) {
                $user = User::create($data);
                $user->sendEmailVerificationNotification();
            });
            return redirect()->route('login')->with([
                'success' => 'Tạo tài khoản thành công!'
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with([
                'error' => 'Lỗi thêm vào cơ sở dữ liệu. Vui lòng thử lại!'
            ]);
        }
    }
    public function logOut(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login')->with('success', 'Bạn đã đăng xuất thành công!');
    }
}
