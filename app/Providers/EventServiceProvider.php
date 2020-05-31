<?php

namespace App\Providers;

use App\Listeners\TwoFactorEnabledRecoveryCodesEmail;
use App\Listeners\TwoFactorRecoveryCodesDepletedEmail;
use DarkGhostHunter\Laraguard\Events\TwoFactorEnabled;
use DarkGhostHunter\Laraguard\Events\TwoFactorRecoveryCodesDepleted;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TwoFactorEnabled::class => [
            TwoFactorEnabledRecoveryCodesEmail::class,
        ],
        TwoFactorRecoveryCodesDepleted::class => [
            TwoFactorRecoveryCodesDepletedEmail::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
