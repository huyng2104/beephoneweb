<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't apply to admin routes or login routes
        if ($request->is('admin*') || $request->is('login*') || $request->is('logout*')) {
            return $next($request);
        }

        $maintenance = \App\Models\Setting::where('key', 'maintenance_mode')->first();
        
        $isEnabled = false;
        if ($maintenance) {
            $val = $maintenance->value;
            $isEnabled = (isset($val['is_enabled']) && ($val['is_enabled'] === true || $val['is_enabled'] === '1' || $val['is_enabled'] === 1));
        }

        if ($isEnabled) {
            // Check if user is admin
            if (auth()->check()) {
                $user = auth()->user();
                $role = $user->role;
                $roleName = is_object($role) ? $role->name : (string)$role;
                
                if ($roleName === 'admin' || $roleName === 'staff') {
                    return $next($request);
                }
            }

            return response()->view('errors.maintenance', [
                'message' => $maintenance->value['message'] ?? 'BeePhone đang nâng cấp hệ thống định kỳ. Vui lòng quay lại sau!',
                'end_at' => $maintenance->value['end_at'] ?? null,
                'site_settings' => \App\Models\Setting::all()->keyBy('key'),
            ], 533); // Using 533 or 503 is fine, but ensure it's not cached too aggressively
        }

        return $next($request);
    }
}
