<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External\Entry;

use App\Models\List\ExternalProfile;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;

/**
 * This action will create the entries through the username method.
 */
abstract class BaseExternalEntryUnclaimedAction
{
    /**
     * The JSON response of the external API.
     *
     * @var array<string, mixed>|null
     */
    protected ?array $data = null;

    public function __construct(protected ExternalProfile|array $profile) {}

    public function getUsername(): string
    {
        if ($this->profile instanceof ExternalProfile) {
            return $this->profile->name;
        }

        return Arr::get($this->profile, ExternalProfile::ATTRIBUTE_NAME);
    }

    abstract public function getUserId(): ?int;

    /**
     * Get the entries of the response.
     *
     * @return array<int, array<string, mixed>>
     */
    abstract public function getEntries(): array;

    /**
     * @throws RequestException
     */
    abstract protected function makeRequest(): void;
}
