<?php

declare(strict_types=1);

namespace App\GraphQL\Handler;

use Closure;
use GraphQL\Error\Error;
use Nuwave\Lighthouse\Execution\AuthenticationErrorHandler as BaseAuthenticationErrorHandler;

/**
 * Wrap native Laravel authentication exceptions, adding structured data to extensions.
 */
class AuthenticationErrorHandler extends BaseAuthenticationErrorHandler
{
    public function __invoke(?Error $error, Closure $next): ?array
    {
        return $next(null);
    }
}
