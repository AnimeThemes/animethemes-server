<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use App\Models\Auth\User;
use Error;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthResolver.
 */
class AuthResolver
{
    /**
     * Resolve the login route.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     * @return User
     */
    public function login($_, array $args): User
    {
        $guard = Auth::guard(Arr::first(config('sanctum.guard')));

        if( ! $guard->attempt($args)) {
            throw new Error('Invalid credentials.');
        }

        $user = $guard->user();
        assert($user instanceof User, 'Since we successfully logged in, this can no longer be `null`.');

        return $user;
    }

    /**
     * Resolve the logout route.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function logout($_, array $args): ?User
    {
        $guard = Auth::guard(Arr::first(config('sanctum.guard', 'web')));

        $user = $guard->user();
        $guard->logout();

        return $user;
    }
}
