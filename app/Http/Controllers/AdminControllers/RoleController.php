<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::whereNotIn('name', ['user', 'admin'])
            ->orderBy('id', 'desc')
            ->paginate(5);
        return view('admin.roles.index')->with([
            'roles' => $roles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('.', $item->slug)[0]; // Trả về 'product', 'customer', v.v.
        });


        return view('admin.roles.create')->with([
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description
        ];
        $permissionIds = $request->input('permissions', []);
        try {
            DB::transaction(function () use ($data, $permissionIds) {
                $role = Role::create($data);
                if (!empty($permissionIds)) {
                    $role->permissions()->attach($permissionIds);
                }
            });
            return redirect()->route('admin.role.index')->with([
                'success' => 'Thêm vai trò mới'
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);

        // Gom nhóm Permission để làm khung hiển thị
        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('.', $item->slug)[0];
        });

        // Lấy mảng ID các quyền mà vai trò này đang có
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.show', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);

        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('.', $item->slug)[0];
        });

        // Lấy danh sách ID các quyền mà Role này ĐANG SỞ HỮU (Chuyển thành mảng)
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        $data = $request->validate([
            'name'        => 'required|string|max:50|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên vai trò.',
            'name.unique'   => 'Tên vai trò này đã tồn tại trong hệ thống.',
            'name.max'      => 'Tên vai trò không được vượt quá 50 ký tự.',
        ]);

        $permissionIds = $request->input('permissions', []);

        DB::transaction(function () use ($role, $data, $permissionIds) {
            $role->update($data);
            $role->permissions()->sync($permissionIds);
        });

        return redirect()->route('admin.role.index')->with('success', 'Cập nhật vai trò và phân quyền');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        if ($role->users->isNotEmpty()) {
            return back()->with([
                'error' => 'Không thể xóa do quyền đã được gán với người dùng '
            ]);
        }
        $role->delete();
        return back()->with('success', 'Đã xóa vai trò');
    }

    public function listMembers(Request $request)
    {
        $roles = Role::whereNotIn('name', ['user', 'admin'])
            ->orderBy('id', 'desc')
            ->get();
        $query = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('name', '!=', 'user'); // Lấy những role KHÁC 'user'
            })
            ->orderBy('id', 'desc');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', $search); // ID khớp 100%
            });
        }

        // 🚦 2. Lọc theo trạng thái dropdown
        if ($request->filled('status')) {
            $status = $request->input('status');

            // Bạn quy ước active/locked là gì trong DB (Ví dụ: cột is_active boolean)
            if ($status == 'active') {
                $query->where('status', 'active');
            } elseif ($status == 'inactive') {
                $query->where('status', 'inactive');
            } elseif ($status == 'banned') {
                $query->where('status', 'banned');
            } elseif ($status == 'delete') {
                // 🗑️ CHỈ lấy những người dùng đã bị xóa mềm (deleted_at IS NOT NULL)
                $query->onlyTrashed();
            }
        }
        if ($request->filled('role')) {
            $query->where('role_id', $request->role); // Sửa 'role_id' cho khớp với tên cột trong DB của bạn
        }


        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        return view('admin.members.index')->with([
            'users' => $users,
            'roles' => $roles
        ]);
    }
}
