<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::before(function ($user) {
            $roleValue = $user->role;
            $roleName = is_object($roleValue)
                ? ($roleValue->name ?? $roleValue->name_role ?? null)
                : $roleValue;

            if ($roleName === 'admin') {
                return true;
            }

            return null;
        });

        $permissions = Permission::select('id', 'slug')->get();

        foreach ($permissions as $permission) {
            Gate::define($permission->slug, function ($user) use ($permission) {
                $hasDirectPermission = $user->permissions->contains('slug', $permission->slug);

                if ($hasDirectPermission) {
                    return true;
                }

                $userRole = $user->role;
                if (is_object($userRole) && $userRole->permissions->contains('slug', $permission->slug)) {
                    return true;
                }

                return false;
            });
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