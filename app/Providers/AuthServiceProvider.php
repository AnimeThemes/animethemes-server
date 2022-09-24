<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Auth\User;
use App\Pivots\BasePivot;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Olssonm\Zxcvbn\Rules\Zxcvbn;
use Olssonm\Zxcvbn\Rules\ZxcvbnDictionary;

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
    public function boot(): void
    {
        $this->registerPolicies();

        Password::defaults(
            fn () => Password::min(8)
                ->uncompromised()
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->rules(['confirmed', new Zxcvbn(3), new ZxcvbnDictionary()])
        );

        Gate::guessPolicyNamesUsing(
            function (string $modelClass) {
                if (is_a($modelClass, BasePivot::class, true)) {
                    return Str::of($modelClass)
                        ->replace('Pivots', 'Policies\Pivot')
                        ->append('Policy')
                        ->__toString();
                }

                return Str::of($modelClass)
                    ->replace('Models', 'Policies')
                    ->append('Policy')
                    ->__toString();
            }
        );

        Gate::define('viewNova', fn (User $user) => $user->can('view nova'));
    }
}
