<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External\Entry;

use App\Models\List\External\ExternalToken;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Crypt;

/**
 * Class BaseExternalEntryClaimedAction.
 *
 * This action will create the entries through the authentication method.
 */
abstract class BaseExternalEntryClaimedAction
{
    /**
     * The JSON response of the external API.
     *
     * @var array<string, mixed>|null
     */
    protected ?array $data = null;

    /**
     * The id of the external user.
     *
     * @var int|null
     */
    protected ?int $userId = null;

    /**
     * Create a new action instance.
     *
     * @param  ExternalToken  $token
     */
    public function __construct(protected ExternalToken $token) {}

    /**
     * Get the id of the external user.
     *
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * Get the token to the request.
     *
     * @return string
     */
    protected function getToken(): string
    {
        return Crypt::decrypt($this->token->access_token);
    }

    /**
     * Get the username.
     *
     * @return string|null
     */
    abstract public function getUsername(): ?string;

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
