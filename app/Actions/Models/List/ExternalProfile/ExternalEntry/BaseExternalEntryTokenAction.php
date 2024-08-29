<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalEntry;

/**
 * Class BaseExternalEntryTokenAction
 *
 * This action will create the entries through the authentication method.
 */
abstract class BaseExternalEntryTokenAction
{
    /**
     * Create a new action instance.
     *
     * @param  array  $parameters
     */
    public function __construct(protected array $parameters)
    {
    }

    /**
     * Get the username of the profile.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return ''; // TODO
    }

    /**
     * Get the token to the request.
     *
     * @return string
     */
    public function getToken(): string
    {
        return ''; // TODO
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
