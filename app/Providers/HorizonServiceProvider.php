<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Auth\Team;
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

        Horizon::night();
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
        Gate::define('viewHorizon', function (User $user) {
            $horizonTeam = Team::query()->find(Config::get('teams.horizon'));

            return $user->isCurrentTeam($horizonTeam);
        });
    }
}
