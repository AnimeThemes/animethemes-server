<?php

declare(strict_types=1);

namespace App\Exceptions\GraphQL;

use Exception;
use GraphQL\Error\ClientAware;

/**
 * Thrown when client arguments are missing or wrong.
 */
class ClientValidationException extends Exception implements ClientAware
{
    public function isClientSafe(): bool
    {
        return true;
    }
}
