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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login()
    {
        if (!Auth::check()) {
            return view('auth.login.index');
        }
        return redirect()->route('home');
    }
    public function adminLogin(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $role = $user?->role?->name ?? $user?->role_slug ?? null;
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home');
        }

        return view('auth.login.admin');
    }
    public function register()
    {
        if (!Auth::check()) {
            return view('auth.register.index');
        }
        return redirect()->route('/');
    }
    public function adminRegister(Request $request)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        if (Auth::check()) {
            $user = Auth::user();
            $role = $user?->role?->name ?? $user?->role_slug ?? null;
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home');
        }

        return view('auth.register.admin');
    }

    public function postAdminLogin(LoginRequest $request)
    {
        $request->merge(['admin_login' => '1']);
        $request->session()->put('admin_login_attempt', true);
        if (!$request->session()->has('url.intended')) {
            $request->session()->put('url.intended', route('admin.dashboard'));
        }

        return $this->postLogin($request);
    }
    public function postLogin(LoginRequest $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        $status = User::select('status', 'email_verified_at')->where('email', $request->email)->first();
        if ($status->status != 'active') {
            return back()->withErrors([
                'email' => 'Tài khoản hiện chưa kích hoạt hoặc bị khóa'
            ])->onlyInput('email');
        }

        $intended = $request->session()->get('url.intended');
        $intendedPath = is_string($intended) ? (parse_url($intended, PHP_URL_PATH) ?: '') : '';
        $isAdminAttempt = (bool) $request->boolean('admin_login')
            || (bool) $request->session()->get('admin_login_attempt', false)
            || ($intendedPath !== '' && str_starts_with($intendedPath, '/admin'));

        // Block non-admin accounts from logging in when trying to access /admin
        if ($isAdminAttempt) {
            $user = User::query()->with('role')->where('email', $request->email)->first();
            $role = $user?->role?->name ?? $user?->role_slug ?? null;

            if ($role !== 'admin') {
                $request->session()->forget('url.intended');
                $request->session()->forget('admin_login_attempt');
                return back()
                    ->with(['error' => 'tk user không được đăng nhập vào admin'])
                    ->onlyInput('email');
            }
        }

        if (Auth::attempt($data)) {
            $request->session()->regenerate();
            activity_log('login', 'Đăng nhập hệ thống');

            $user = Auth::user();
            $role = $user?->role?->name ?? $user?->role_slug ?? null;

            // If a normal user tries to enter /admin via intended redirect, kick them back to login.
            if ($intendedPath !== '' && str_starts_with($intendedPath, '/admin') && $role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')->with('error', 'tk user không được đăng nhập vào admin');
            }

            $request->session()->forget('admin_login_attempt');
            return redirect()->intended('/')->with(['success' => 'Đăng nhập thành công']);
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
    public function postAdminRegister(StoreRegister $request)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $role = Role::where('name', 'admin')->first();
        if (!$role) {
            return back()->withInput()->with([
                'error' => 'Không tìm thấy quyền admin'
            ]);
        }

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
            return redirect()->route('admin.login')->with([
                'success' => 'Tạo tài khoản admin thành công!'
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with([
                'error' => 'Lỗi thêm vào cơ sở dữ liệu. Vui lòng thử lại!'
            ]);
        }
    }

    public function logOut(Request $request)
    {
        activity_log('logout', 'Đăng xuất');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login')->with('success', 'Bạn đã đăng xuất thành công!');
    }

    public function resetPassword()
    {
        return view('auth.reset_password.index');
    }

    public function postResetPassword(Request $request)
    {

        $request->validate(
            [
                'email' => 'required|email|exists:users,email',
            ],
            [
                'email.required' => 'Email là bắt buộc',
                'email.email' => 'Email không đúng định dạng',
                'email.exists' => 'Email không tồn tại',
            ]
        );
        $code = mt_rand(100000, 999999);
        session([
            'verify_otp' => $code,
            'verify_expire' => Carbon::now()->addMinutes(5),
            'verify_attempt' => 0,
            'verify_email' => $request->email,
        ]);
        $data =
            [
                'code' => $code,
                'email' => $request->email
            ];
        Mail::send('mails/reset_password', $data, function ($message) use ($request) {
            $message->to($request->email, 'Tên người nhận');
            $message->subject('Khôi phục mật khẩu hệ thống');
            $message->from(
                env('MAIL_FROM_ADDRESS'),
                env('MAIL_FROM_NAME')
            );
            $message->replyTo('support@yourdomain.com', 'Support Team');
            $message->priority(1);
        });
        return redirect()->route('verify-code')->with([
            'success' => "Vui lòng lấy mã trong email"
        ]);
    }

    public function verify_code()
    {
        if (!session('verify_email')) {
            return redirect()->route('/');
        }
        return view('auth.confirm_code.index');
    }

    public function check_otp(Request $request)
    {
        $request->validate([
            'otp' => 'required|array|size:6',
            'otp.*' => 'required|numeric|digits:1'
        ], [
            'otp.*.required' => 'Vui lòng nhập đầy đủ mã OTP',
            'otp.*.numeric' => 'OTP phải là số',
            'otp.*.digits' => 'Mỗi ô OTP chỉ được 1 số'
        ]);
        $otp = implode('', $request->otp);
        $verify_otp = session('verify_otp');
        $verify_attempt = session('verify_attempt');
        $verify_expire = session('verify_expire');
        // $verify_email = session('verify_email');

        // dd($verify_email);
        if (empty($verify_otp)  || $verify_attempt >= 3 || now()->gt($verify_expire)) {
            session()->forget([
                'verify_otp',
                'verify_expire',
                'verify_attempt'
            ]);
            return back()->withErrors([
                'otp' => 'Mã hết hạn vui lòng gửi lại mã'
            ]);
        }

        if ($otp == $verify_otp) {
            $newPassword = Str::random(10);
            $user = User::where('email', session('verify_email'))->first();
            $data = [
                'email' => $user->email,
                'password' => $newPassword
            ];
            if ($user) {
                $user->update([
                    'password' => Hash::make($newPassword)
                ]);

                Mail::send('mails/new_password', $data, function ($message) use ($user) {
                    $message->to($user->email, 'Tên người nhận');
                    $message->subject('Khôi phục mật khẩu hệ thống');
                    $message->from(
                        env('MAIL_FROM_ADDRESS'),
                        env('MAIL_FROM_NAME')
                    );
                    $message->replyTo('support@yourdomain.com', 'Support Team');
                    $message->priority(1);
                });
            }
            session()->forget([
                'verify_otp',
                'verify_expire',
                'verify_attempt',
                'verify_email'
            ]);

            return redirect()->route('login')->with([
                'success' => "Mật khẩu được gửi trong gmail"
            ]);
        } else {
            $verify_attempt = session()->increment('verify_attempt');
            return back()->withErrors([
                'otp' => 'Mã nhập không đúng bạn còn ' . 3 - $verify_attempt . ' nhập'
            ]);
        }
    }
}
