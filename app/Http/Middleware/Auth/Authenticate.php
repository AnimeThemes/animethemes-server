<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson()
            ? null
            : url(Config::get('wiki.login'));
    }
}
