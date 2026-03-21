<?php

namespace App\Providers;

use App\Policies\NotificationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\DatabaseNotification;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //DatabaseNotification::class => NotificationPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
