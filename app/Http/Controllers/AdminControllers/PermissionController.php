<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{
    public function index()
    {
        Gate::authorize('roles.view');
        
        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('.', $item->slug)[0];
        });

        return view('admin.permissions.index', compact('permissions'));
    }
}
