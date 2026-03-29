<?php

namespace App\Http\Controllers\Client;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserClientRequest;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return abort(404);
        }
        return view('client.profiles.index')->with([
            'user' => $user
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserClientRequest $request, string $id)
    {
        $data = $request->except(['avatar']);
        $user = User::findOrFail($id);

        try {
            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    FileHelper::delete($user->avatar);
                }
                $data['avatar'] = FileHelper::upload($request->file('avatar'));
            }
            $user->update($data);

            return back()->with([
                'success' => 'Cập nhật thông tin thành công.'
            ]);
      } catch (\Throwable $th) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

  public function user_wallet()
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(404);
        }

        // 1. Luôn đảm bảo tạo ví nếu chưa có
        Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'status' => 'active']
        );

        // 2. DÒNG BÙA CHÚ GIẢI CỨU: Bắt user load lại cái ví mới tạo!
        $user->load('wallet');

        return view('client.profiles.wallet')->with([
            'user' => $user
        ]);
    }
    public function user_voucher()
    {
        $user = Auth::user();
        return view('client.profiles.voucher')->with([
            'user' => $user
        ]);
    }
    public function passwordUpdate(Request $request,   $id)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
        ]);

        $user = User::findOrFail($id);


        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Mật khẩu hiện tại bạn nhập không chính xác.'
            ]);
        }
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        return back()->with([
            'success' => 'Đổi mật khẩu thành công!'
        ]);
    }
}
