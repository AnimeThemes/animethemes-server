<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalToken;

use App\Models\List\External\ExternalToken;

/**
 * Class BaseExternalTokenAction.
 */
abstract class BaseExternalTokenAction
{
    abstract public function store(): ExternalToken;
}
