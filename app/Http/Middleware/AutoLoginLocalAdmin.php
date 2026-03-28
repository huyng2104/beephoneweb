<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class AutoLoginLocalAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!app()->environment('local')) {
            return $next($request);
        }

        if (Auth::check()) {
            return $next($request);
        }

        if (!Schema::hasTable('users')) {
            return $next($request);
        }

        $user = User::query()->where('email', 'admin@local.test')->first();

        if (!$user) {
            $user = User::query()->create([
                'name' => 'Local Admin',
                'email' => 'admin@local.test',
                'password' => Hash::make('password'),
                'status' => Schema::hasColumn('users', 'status') ? 'active' : null,
                'email_verified_at' => now(),
            ]);
        }

        // Best-effort: attach admin role if schema supports it.
        if (Schema::hasColumn('users', 'role_slug') && empty($user->role_slug)) {
            $user->role_slug = 'admin';
        }

        if (Schema::hasColumn('users', 'role_id') && empty($user->role_id) && Schema::hasTable('roles')) {
            $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
            if ($adminRoleId) {
                $user->role_id = $adminRoleId;
            }
        }

        $user->save();
        Auth::login($user);

        return $next($request);
    }
}

