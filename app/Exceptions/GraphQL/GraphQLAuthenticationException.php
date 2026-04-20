<?php

declare(strict_types=1);

namespace App\Exceptions\GraphQL;

use Nuwave\Lighthouse\Exceptions\AuthenticationException;

class GraphQLAuthenticationException extends AuthenticationException
{
    /** @return array{guards: array<string>} */
    public function getExtensions(): array
    {
        return [
            'guards' => $this->guards,
            'category' => 'authorization',
        ];
    }
}
