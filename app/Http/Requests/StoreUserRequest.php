<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('customer.view');
        $query = User::orderBy('id', 'desc');
        $users = $query->paginate(10);
        $totalStaff = User::whereHas('role', function ($query) {
            $query->where('name', 'staff');
        })->count();
        $totalBanned = User::where('status', 'banned')->count();
        return view('admin.users.index')->with([
            'users' => $users,
            'totalStaff' => $totalStaff,
            'totalBanned' => $totalBanned
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::select('id', 'name')->get();
        $permissions = Permission::all();
        return view('admin.users.create')->with([
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {

        $path_avatar = null;

        if ($request->hasFile('avatar')) {
            $path_avatar = FileHelper::upload($request->file('avatar'), 'avatar');
        }

        $role = Role::find($request->role);
        $user_permissions = $role->name == 'staff'
            ? ($request->permissions ?? [])
            : [];

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'avatar'   => $path_avatar ?? null,
            'status'   => $request->status == 1 ? 'active' : 'inactive',
            'password' => Hash::make($request->password),
            'role_id'  => $request->role,

            // Bổ sung thêm các trường mới tại đây
            'gender'   => $request->gender,   // Giới tính
            'birthday'      => $request->dob,      // Ngày tháng năm sinh (Date of Birth)
            'address'  => $request->address,  // Địa chỉ
        ];

        try {
            DB::transaction(function () use ($data, $user_permissions) {
                $user = User::create($data);
                $user->permissions()->sync($user_permissions);
            });

            return redirect()->route('admin.users.index')->with([
                'success' => 'Thêm người dùng thành công!'
            ]);
        } catch (\Exception $e) {
            if ($path_avatar) {
                FileHelper::delete($path_avatar);
            }

            return back()->withInput()->with([
                'error' => 'Lỗi thêm vào cơ sở dữ liệu. Vui lòng thử lại!'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        Gate::authorize('customer.view');
        $user = User::findOrFail($id);
        return view('admin.users.show')->with([
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        Gate::authorize('customer.update');
        $roles = Role::select('id', 'name')->get();
        $user = User::findOrFail($id);
        $permissions = Permission::all();
        return view('admin.users.edit')->with([
            'roles' => $roles,
            'user' => $user,
            'permissions' => $permissions
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $old_avatar = $user->avatar;
        $path_avatar = $user->avatar;
        if ($request->hasFile('avatar')) {
            $path_avatar = FileHelper::upload($request->file('avatar'), 'avatar');
        }

        $role = Role::find($request->role);
        $user_permissions = ($role && $role->name == 'staff')
            ? ($request->permissions ?? [])
            : [];

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'avatar' => $path_avatar,
            'status' => $request->status == 1 ? 'active' : 'inactive',
            'role_id' => $request->role,
            'gender'   => $request->gender,   // Giới tính
            'birthday'      => $request->dob,      // Ngày tháng năm sinh (Date of Birth)
            'address'  => $request->address,  // Địa chỉ
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        try {
            DB::transaction(function () use ($user, $data, $user_permissions) {

                $user->update($data);
                $user->permissions()->sync($user_permissions);
            });

            if ($request->hasFile('avatar') && $old_avatar) {
                FileHelper::delete($old_avatar);
            }

            return redirect()->route('admin.users.index')->with([
                'success' => 'Cập nhật người dùng thành công!'
            ]);
        } catch (\Exception $e) {

            if ($request->hasFile('avatar') && $path_avatar) {
                FileHelper::delete($path_avatar);
            }

            return back()->with([
                'error' => 'Lỗi cập nhật vào db'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return back()->with('success', 'Đã xóa người dùng');
    }
    public function block($id)
    {
        Gate::authorize('customer.lock');
        $user = User::findOrFail($id);
        $user->update([
            'status' => 'banned'
        ]);
        return back()->with('success', 'Đã chặn người dùng');
    }
    public function unBlock($id)
    {
        Gate::authorize('customer.lock');
        $user = User::findOrFail($id);
        $user->update([
            'status' => 'active'
        ]);
        return back()->with('success', 'Đã khôi phục người dùng');
    }
    public function resetPw($id){
        $user = User::findOrFail($id);
        $password = Str::random(8);
        $user->update([
            'password' => $password
        ]);
        return back()->with([
            'success' => 'Đã khôi phục mật khẩu',
            'new_password' => $password
        ]);
    }
}