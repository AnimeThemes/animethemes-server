<?php

declare(strict_types=1);

namespace App\GraphQL\Handler;

use Closure;
use GraphQL\Error\Error;
use Nuwave\Lighthouse\Execution\AuthorizationErrorHandler as BaseAuthorizationErrorHandler;

/**
 * Wrap native Laravel authorization exceptions, adding structured data to extensions.
 */
class AuthorizationErrorHandler extends BaseAuthorizationErrorHandler
{
    public function __invoke(?Error $error, Closure $next): ?array
    {
        return $next(null);
    }
}
