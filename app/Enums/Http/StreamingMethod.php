<?php

declare(strict_types=1);

namespace App\Enums\Http;

use App\Enums\BaseEnum;

/**
 * Class StreamingMethod.
 */
class StreamingMethod extends BaseEnum
{
    public const NGINX = 'nginx';
    public const RESPONSE = 'response';
}
