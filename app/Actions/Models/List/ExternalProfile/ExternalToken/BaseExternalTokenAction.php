<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalToken;

use App\Models\List\External\ExternalToken;
use Exception;

/**
 * Class BaseExternalTokenAction.
 */
abstract class BaseExternalTokenAction
{
    /**
     * Use the authorization code to get the tokens and store them.
     *
     * @param  array  $parameters
     * @return ExternalToken
     *
     * @throws Exception
     */
    abstract public function store(array $parameters): ExternalToken;
}
