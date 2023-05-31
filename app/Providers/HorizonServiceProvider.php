<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

/**
 * Class HorizonServiceProvider.
 */
class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        Horizon::routeMailNotificationsTo(Config::get('mail.from.address'));
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', fn (User $user) => $user->can(SpecialPermission::VIEW_HORIZON));
    }
}
