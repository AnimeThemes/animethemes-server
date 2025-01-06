<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalEntry;

use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;

/**
 * Class BaseExternalEntryAction.
 *
 * This action will create the entries through the username method.
 */
abstract class BaseExternalEntryAction
{
    protected ?array $response = null;

    /**
     * Create a new action instance.
     *
     * @param  ExternalProfile|array  $profile
     */
    public function __construct(protected ExternalProfile|array $profile)
    {
    }

    /**
     * Get the profile site.
     *
     * @return ExternalProfileSite
     */
    public function getProfileSite(): ExternalProfileSite
    {
        if ($this->profile instanceof ExternalProfile) {
            return $this->profile->site;
        }

        // TODO: change 'site' to a constant variable in API.
        return ExternalProfileSite::fromLocalizedName(Arr::get($this->profile, 'site'));
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
        if ($this->profile instanceof ExternalProfile) {
            return $this->profile->name;
        }

        // TODO: change 'name' to a constant variable in API.
        return Arr::get($this->profile, 'name');
    }

    /**
     * Get the id of the external user.
     *
     * @return int|null
     */
    abstract public function getId(): ?int;

    /**
     * Get the entries of the response.
     *
     * @return array
     */
    abstract public function getEntries(): array;

    /**
     * Make the request to the external api.
     *
     * @return static
     */
    abstract protected function makeRequest(): static;
}
