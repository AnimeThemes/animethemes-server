<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External\Token;

use App\Models\List\External\ExternalToken;
use Exception;

abstract class BaseExternalTokenAction
{
    /**
     * Use the authorization code to get the tokens and store them.
     *
     *
     * @throws Exception
     */
    abstract public function store(array $parameters): ExternalToken;
}
