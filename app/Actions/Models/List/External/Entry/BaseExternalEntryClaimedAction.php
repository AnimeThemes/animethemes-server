<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External\Entry;

use App\Models\List\External\ExternalToken;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Crypt;

/**
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
     */
    protected ?int $userId = null;

    public function __construct(protected ExternalToken $token) {}

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    protected function getToken(): string
    {
        return Crypt::decrypt($this->token->access_token);
    }

    abstract public function getUsername(): ?string;

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
