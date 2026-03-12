<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;

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
            if ($user->role->name === 'admin') {
                return true;
            }
        });
        $permissions = Permission::select('id', 'slug')->get();
        foreach ($permissions as $permission) {

            Gate::define($permission->slug, function ($user) use ($permission) {

                return $user->permissions->contains('id', $permission->id);
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
