<?php

declare(strict_types=1);

namespace App\GraphQL\Handler;

use App\Exceptions\GraphQL\GraphQLAuthenticationException;
use Closure;
use GraphQL\Error\Error;
use Illuminate\Auth\AuthenticationException as LaravelAuthenticationException;
use Nuwave\Lighthouse\Execution\AuthenticationErrorHandler as BaseAuthenticationErrorHandler;

/**
 * Wrap native Laravel authentication exceptions, adding structured data to extensions.
 */
class AuthenticationErrorHandler extends BaseAuthenticationErrorHandler
{
    public function __invoke(?Error $error, Closure $next): ?array
    {
        if ($error instanceof Error) {
            $underlyingException = $error->getPrevious();

            if ($underlyingException instanceof LaravelAuthenticationException) {
                $error = new Error(
                    $error->getMessage(),
                    $error->getNodes(),
                    $error->getSource(),
                    $error->getPositions(),
                    $error->getPath(),
                    GraphQLAuthenticationException::fromLaravel($underlyingException),
                );
            }
        }

        return $next($error);
    }
}
