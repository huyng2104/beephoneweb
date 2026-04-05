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
    public function register()
    {
        if (!Auth::check()) {
            return view('auth.register.index');
        }
        return redirect()->route('/');
    }
    public function postLogin(LoginRequest $request)
    {
        $user = User::withTrashed()->where('email', $request->email)->first();
        if ($user->trashed()) {
            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị xóa khỏi hệ thống.'
            ])->onlyInput('email');
        }
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

        if (Auth::attempt($data)) {
            $request->session()->regenerate();
            activity('auth')
                ->causedBy($user) // Xác định người vừa đăng nhập
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(), // Lưu thông tin trình duyệt, thiết bị
                    'status'     => 'success'
                ])
                ->log('Đăng nhập hệ thống thành công.');
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
        $user = Auth::id();
        if ($user) {
        // Thực hiện ghi log
        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'ip_address' => $request->ip(),
            ])
            ->log('Đã đăng xuất khỏi hệ thống.');
    }
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
