<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ActivityLog;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = ['user', 'employee', 'admin'];
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'] ?? 'user',
        ]);

        return redirect('/admin/users')->with('success', 'Tạo người dùng thành công');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = ['user', 'employee', 'admin'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->has('role')) {
            $validated['role'] = $request->input('role');
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);


        return redirect('/admin/users')->with('success', 'Cập nhật người dùng thành công');
    }

    public function toggleLock(User $user)
    {
        $user->is_locked = ! $user->is_locked;
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái khóa tài khoản thành công');
    }
    public function trash()
    {
    $users = User::onlyTrashed()->get();

    return view('admin.users.trash', compact('users'));
    }
    public function restore($id)
    {
    $user = User::onlyTrashed()->findOrFail($id);

    $user->restore();

    return redirect()->back()->with('success','Khôi phục thành công');
    }
    public function forceDelete($id)
{
    $user = User::onlyTrashed()->findOrFail($id);

    $user->forceDelete();

    return redirect()->back()->with('success','Đã xóa vĩnh viễn');
}
public function destroy($id)
{
    $user = User::findOrFail($id);

    $user->delete();

    return redirect()
        ->route('admin.users.index')
        ->with('success','Đã chuyển user vào thùng rác');
}
}
