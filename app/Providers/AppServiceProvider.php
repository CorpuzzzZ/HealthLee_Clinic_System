<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\Appointment;
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

        // Share pending appointments count for doctor's navigation
        View::composer('layouts.navigation', function ($view) {
            $pendingAppointmentsCount = 0;
            
            if (Auth::check() && Auth::user()->role === 'doctor') {
                $pendingAppointmentsCount = Appointment::where('doctor_id', Auth::user()->doctor->id)
                    ->where('status', 'pending')
                    ->count();
            }
            
            $view->with('pendingAppointmentsCount', $pendingAppointmentsCount);
        });
    }
}