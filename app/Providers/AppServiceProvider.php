<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share unread notification count with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $unreadNotifications = Notification::where('user_id', Auth::id())
                    ->where('status', 'unread')
                    ->count();
                $view->with('unreadNotifications', $unreadNotifications);
            }
        });
    }
}