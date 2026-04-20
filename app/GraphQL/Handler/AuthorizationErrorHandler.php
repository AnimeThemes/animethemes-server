<?php

declare(strict_types=1);

namespace App\GraphQL\Handler;

use App\Exceptions\GraphQL\GraphQLAuthorizationException;
use Closure;
use GraphQL\Error\Error;
use Illuminate\Auth\Access\AuthorizationException as LaravelAuthorizationException;
use Nuwave\Lighthouse\Execution\AuthorizationErrorHandler as BaseAuthorizationErrorHandler;

/**
 * Wrap native Laravel authorization exceptions, adding structured data to extensions.
 */
class AuthorizationErrorHandler extends BaseAuthorizationErrorHandler
{
    public function __invoke(?Error $error, Closure $next): ?array
    {
        if ($error instanceof Error) {
            $underlyingException = $error->getPrevious();

            if ($underlyingException instanceof LaravelAuthorizationException) {
                $error = new Error(
                    $error->getMessage(),
                    $error->getNodes(),
                    $error->getSource(),
                    $error->getPositions(),
                    $error->getPath(),
                    GraphQLAuthorizationException::fromLaravel($underlyingException),
                );
            }
        }

        return $next($error);
    }
}
