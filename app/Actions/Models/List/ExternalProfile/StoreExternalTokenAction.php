<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile;

use App\Actions\Models\List\ExternalProfile\ExternalToken\BaseExternalTokenAction;
use App\Actions\Models\List\ExternalProfile\ExternalToken\Site\AnilistExternalTokenAction;
use App\Enums\Models\List\ExternalProfileSite;
use Illuminate\Support\Arr;

/**
 * Class StoreExternalTokenAction.
 */
class StoreExternalTokenAction
{
    public function store($query)
    {
        $profileSite = ExternalProfileSite::fromLocalizedName(Arr::get($query, 'site'));
        $code = Arr::get($query, 'code');

        $action = $this->getActionClass($profileSite, $code);

        $externalToken = $action->store();
    }

    /**
     * Get the mapping for the token class.
     *
     * @param  ExternalProfileSite  $site
     * @param  string  $code
     * @return BaseExternalTokenAction|null
     */
    protected function getActionClass(ExternalProfileSite $site, string $code): ?BaseExternalTokenAction
    {
        return match ($site) {
            ExternalProfileSite::ANILIST => new AnilistExternalTokenAction($code),
            default => null,
        };
    }

}
