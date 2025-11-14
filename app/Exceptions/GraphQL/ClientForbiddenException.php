<?php

declare(strict_types=1);

namespace App\Exceptions\GraphQL;

use GraphQL\Error\Error;

/**
 * Thrown when query is not allowed.
 */
class ClientForbiddenException extends Error
{
    public function isClientSafe(): bool
    {
        return true;
    }
}
