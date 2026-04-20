<?php

declare(strict_types=1);

namespace App\Exceptions\GraphQL;

use GraphQL\Error\ProvidesExtensions;
use Nuwave\Lighthouse\Exceptions\AuthorizationException;

class GraphQLAuthorizationException extends AuthorizationException implements ProvidesExtensions
{
    public function getExtensions(): array
    {
        return [
            'category' => 'authorization',
        ];
    }
}
