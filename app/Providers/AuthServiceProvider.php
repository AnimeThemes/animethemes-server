<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Auth\Team;
use App\Models\Auth\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * Class AuthServiceProvider.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Password::defaults(
            fn () => Password::min(8)
                ->uncompromised()
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
        );

        Gate::guessPolicyNamesUsing(
            fn (string $modelClass) => Str::of($modelClass)
                ->replace('Models', 'Policies')
                ->append('Policy')
                ->__toString()
        );

        Gate::define('viewNova', function (User $user) {
            $novaTeam = Team::query()->find(Config::get('teams.nova'));

            return $user->isCurrentTeam($novaTeam);
        });
    }
}
