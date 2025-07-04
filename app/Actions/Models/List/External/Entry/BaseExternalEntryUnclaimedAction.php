<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External\Entry;

use App\Models\List\ExternalProfile;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;

/**
 * Class BaseExternalEntryUnclaimedAction.
 *
 * This action will create the entries through the username method.
 */
abstract class BaseExternalEntryUnclaimedAction
{
    /**
     * The JSON response of the external API.
     *
     * @var array|null
     */
    protected ?array $data = null;

    /**
     * Create a new action instance.
     *
     * @param  ExternalProfile|array  $profile
     */
    public function __construct(protected ExternalProfile|array $profile) {}

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

        return Arr::get($this->profile, ExternalProfile::ATTRIBUTE_NAME);
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
     * @return array<int, array<string, mixed>>
     */
    abstract public function getEntries(): array;

    /**
     * Make the request to the external api.
     *
     * @return void
     *
     * @throws RequestException
     */
    abstract protected function makeRequest(): void;
}
