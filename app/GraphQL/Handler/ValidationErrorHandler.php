<?php

declare(strict_types=1);

namespace App\GraphQL\Handler;

use Closure;
use GraphQL\Error\Error;
use Nuwave\Lighthouse\Execution\ValidationErrorHandler as BaseValidationErrorHandler;

/**
 * Wrap native Laravel validation exceptions, adding structured data to extensions.
 */
class ValidationErrorHandler extends BaseValidationErrorHandler
{
    public function __invoke(?Error $error, Closure $next): ?array
    {
        return $next(null);
    }
}
