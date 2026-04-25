<?php

namespace App\Providers;

use App\Policies\NotificationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //DatabaseNotification::class => NotificationPolicy::class,
    ];

    public function boot(): void
    {
        //
        Gate::define('viewLarecipe', function (?User $user, $documentation) {
            if (!$user) {
                return false;
            }

            $title = $documentation->title;

            return match (true) {
                $user->hasRole('admin') => true,
                $user->hasRole('manager') => str_starts_with($title, 'Manager') || str_starts_with($title, 'Descripción'),
                $user->hasRole('team-leader') => str_starts_with($title, 'Líder') || str_starts_with($title, 'Descripción'),
                $user->hasRole('user') => str_starts_with($title, 'Usuario') || str_starts_with($title, 'Descripción'),
                default => false,
            };
        });
    }
}
