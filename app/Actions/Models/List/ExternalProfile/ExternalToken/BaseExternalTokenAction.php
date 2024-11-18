<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalToken;

use App\Models\List\External\ExternalToken;

/**
 * Class BaseExternalTokenAction.
 */
abstract class BaseExternalTokenAction
{
    /**
     * Create a new action instance.
     */
    public function __construct()
    {
    }

    /**
     * Use the authorization code to get the tokens and store them.
     *
     * @param  array  $parameters
     * @return ExternalToken|null
     */
    abstract public function store(array $parameters): ?ExternalToken;
}
