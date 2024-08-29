<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile;

use App\Actions\Models\List\ExternalProfile\ExternalToken\BaseExternalTokenAction;
use App\Actions\Models\List\ExternalProfile\ExternalToken\Site\AnilistExternalTokenAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Models\List\External\ExternalToken;
use Error;
use Exception;
use Illuminate\Support\Arr;

/**
 * Class StoreExternalTokenAction.
 */
class StoreExternalTokenAction
{
    /**
     * Store the token given the query of the callback URL.
     *
     * @param  array  $query
     * @return ExternalToken|null
     *
     * @throws Exception
     */
    public function store(array $query): ?ExternalToken
    {
        $site = Arr::get($query, 'site');
        $profileSite = ExternalProfileSite::fromLocalizedName($site);

        $action = $this->getActionClass($profileSite);

        if ($action === null) {
            throw new Error("Undefined callback URL for site {$site}");
        }

        $externalToken = $action->store(Arr::get($query, 'code'));

        return $externalToken;
    }

    /**
     * Get the mapping for the token class.
     *
     * @param  ExternalProfileSite  $site
     * @return BaseExternalTokenAction|null
     */
    protected function getActionClass(ExternalProfileSite $site): ?BaseExternalTokenAction
    {
        return match ($site) {
            ExternalProfileSite::ANILIST => new AnilistExternalTokenAction(),
            default => null,
        };
    }
}
