<?php

declare(strict_types=1);

namespace App\GraphQL\Handler;

use App\Exceptions\GraphQL\GraphQLValidationException;
use Closure;
use GraphQL\Error\Error;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Nuwave\Lighthouse\Execution\ValidationErrorHandler as BaseValidationErrorHandler;

/**
 * Wrap native Laravel validation exceptions, adding structured data to extensions.
 */
class ValidationErrorHandler extends BaseValidationErrorHandler
{
    public function __invoke(?Error $error, Closure $next): ?array
    {
        if ($error instanceof Error) {
            $underlyingException = $error->getPrevious();

            if ($underlyingException instanceof LaravelValidationException) {
                $error = new Error(
                    $error->getMessage(),
                    $error->getNodes(),
                    $error->getSource(),
                    $error->getPositions(),
                    $error->getPath(),
                    GraphQLValidationException::fromLaravel($underlyingException),
                );
            }
        }

        return $next($error);
    }
}
