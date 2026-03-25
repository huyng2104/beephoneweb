<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Local dev: allow everything so you can access admin screens without setting up full RBAC.
        Gate::before(function ($user, $ability) {
            if (app()->environment('local')) {
                return true;
            }

            // Production-ish behavior: allow admins.
            $roleName = $user->role?->name ?? $user->role_slug ?? null;
            if ($roleName === 'admin') {
                return true;
            }

            return null;
        });

        // Only define permission gates if the table exists (prevents boot-time SQL errors on fresh DBs).
        if (Schema::hasTable('permissions')) {
            $permissions = Permission::select('id', 'slug')->get();

            foreach ($permissions as $permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->permissions->contains('id', $permission->id);
                });
            }
        }

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Xác minh email')
                ->greeting('Xin chào!')
                ->line('Vui lòng xác minh email để kích hoạt tài khoản.')
                ->action('Xác minh email', $url)
                ->line('Cảm ơn bạn đã đăng ký!');
        });
    }
}
