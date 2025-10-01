<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Password::defaults(
            fn () => Password::min(8)
                ->uncompromised()
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->rules('confirmed')
        );

        ResetPassword::createUrlUsing(fn (mixed $user, string $token) => url(Config::get('wiki.reset_password'))."?token=$token");

        Gate::guessPolicyNamesUsing(
            fn (string $modelClass) => Str::of($modelClass)
                ->replace('Models', 'Policies')
                ->append('Policy')
                ->__toString()
        );
    }
}
