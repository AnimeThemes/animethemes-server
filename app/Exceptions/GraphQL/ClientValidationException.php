<?php

declare(strict_types=1);

namespace App\Exceptions\GraphQL;

use GraphQL\Error\Error;

/**
 * Thrown when client arguments are missing or wrong.
 */
class ClientValidationException extends Error
{
    public function isClientSafe(): bool
    {
        return true;
    }
}
