<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Auth\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
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
        Password::defaults(
            fn () => Password::min(8)
                ->uncompromised()
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->rules(['confirmed', new Zxcvbn(3), new ZxcvbnDictionary()])
        );

        ResetPassword::createUrlUsing(function (mixed $user, string $token) {
            return url(Config::get('wiki.reset_password'), ['token' => $token]);
        });

        Gate::guessPolicyNamesUsing(
            fn (string $modelClass) => Str::of($modelClass)
                ->replace('Models', 'Policies')
                ->append('Policy')
                ->__toString()
        );

        Gate::define('viewNova', fn (User $user) => $user->can('view nova'));
    }
}
