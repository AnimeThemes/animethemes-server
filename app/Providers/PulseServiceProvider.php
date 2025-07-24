<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Pulse\PulseServiceProvider as BasePulseServiceProvider;

class PulseServiceProvider extends BasePulseServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        Gate::define('viewPulse', fn (User $user) => $user->can(SpecialPermission::VIEW_PULSE->value));
    }
}
