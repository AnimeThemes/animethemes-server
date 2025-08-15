<?php

declare(strict_types=1);

namespace App\Exceptions\GraphQL;

use Exception;
use GraphQL\Error\ClientAware;

/**
 * Thrown when query is not allowed.
 */
class ClientForbiddenException extends Exception implements ClientAware
{
    public function isClientSafe(): bool
    {
        return true;
    }
}
