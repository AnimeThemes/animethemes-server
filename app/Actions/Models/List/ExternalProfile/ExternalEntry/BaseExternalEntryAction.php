<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalEntry;

use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\Wiki\ResourceSite;
use Illuminate\Support\Arr;

/**
 * Class BaseExternalEntryAction.
 *
 * This action will create the entries through the username method.
 */
abstract class BaseExternalEntryAction
{
    /**
     * Create a new action instance.
     *
     * @param  array  $profileParameters
     */
    public function __construct(protected array $profileParameters)
    {
    }

    /**
     * Get the profile site.
     *
     * @return ExternalProfileSite
     */
    public function getProfileSite(): ExternalProfileSite
    {
        return ExternalProfileSite::fromLocalizedName(Arr::get($this->profileParameters, 'site'));
    }

    /**
     * Get the resource site of the profile site.
     *
     * @return ResourceSite
     */
    protected function getResourceSite(): ResourceSite
    {
        return $this->getProfileSite()->getResourceSite();
    }

    /**
     * Get the username of the profile.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return Arr::get($this->profileParameters, 'name');
    }

    /**
     * Get the entries of the response.
     *
     * @return array
     */
    abstract public function getEntries(): array;

    /**
     * Make the request to the external api.
     *
     * @return array|null
     */
    abstract public function makeRequest(): ?array;
}
