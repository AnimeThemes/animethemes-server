<?php

declare(strict_types=1);

namespace App\GraphQL\ErrorHandler;

use Closure;
use GraphQL\Error\Error;
use Nuwave\Lighthouse\Execution\ErrorHandler;
use Typesense\Exceptions\TypesenseClientError;

class TypesenseErrorHandler implements ErrorHandler
{
    public function __invoke(?Error $error, Closure $next): ?array
    {
        $underlyingException = $error?->getPrevious();
        if ($underlyingException instanceof TypesenseClientError) {
            return $next(new Error(
                'Sorting by this value is not supported when using the \'search\' parameter.',
                $error->getNodes(),
                $error->getSource(),
                $error->getPositions(),
                $error->getPath(),
            ));
        }

        return $next($error);
    }
}
