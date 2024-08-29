<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalToken\Site;

use App\Actions\Models\List\ExternalProfile\ExternalToken\BaseExternalTokenAction;
use App\Models\List\External\ExternalToken;

/**
 * Class AnilistExternalTokenAction.
 */
class AnilistExternalTokenAction extends BaseExternalTokenAction
{
    /**
     * Create a new action instance.
     *
     * @param  string  $code
     */
    public function __construct(protected string $code)
    {
    }

    public function store(): ExternalToken
    {
        // TODO: Make a request to the AniList API to get the access and the refresh tokens
        // and return the external token created.
        return new ExternalToken(); 
    }
}
