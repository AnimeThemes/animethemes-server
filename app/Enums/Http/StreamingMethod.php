<?php

declare(strict_types=1);

namespace App\Enums\Http;

/**
 * Enum StreamingMethod.
 */
enum StreamingMethod: string
{
    case NGINX = 'nginx';
    case RESPONSE = 'response';
}
