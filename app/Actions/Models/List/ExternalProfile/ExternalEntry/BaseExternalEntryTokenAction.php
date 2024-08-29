<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalEntry;

use App\Models\List\External\ExternalToken;

/**
 * Class BaseExternalEntryTokenAction
 *
 * This action will create the entries through the authentication method.
 */
abstract class BaseExternalEntryTokenAction
{
    protected ?array $response = null;

    /**
     * Create a new action instance.
     */
    public function __construct(protected ExternalToken $token)
    {
    }

    /**
     * Get the id of the external user.
     *
     * @return int
     */
    public function getId(): int
    {
        return 0; // TODO: Decode the token using JWT
    }

    /**
     * Get the token to the request.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token->access_token;
    }

    /**
     * Get the username.
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return null;
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
     * @return static
     */
    abstract protected function makeRequest(): static;
}
