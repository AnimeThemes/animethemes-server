<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External\Entry\Claimed;

use App\Actions\Models\List\External\Entry\BaseExternalEntryClaimedAction;
use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Models\List\External\ExternalEntry;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class MalExternalEntryClaimedAction.
 */
class MalExternalEntryClaimedAction extends BaseExternalEntryClaimedAction
{
    /**
     * The response of the user endpoint in external API.
     *
     * @var array|null
     */
    protected ?array $userResponse = null;

    /**
     * Get the entries of the response.
     *
     * @return array
     */
    public function getEntries(): array
    {
        $entries = [];

        if ($this->response === null) {
            $this->makeRequest();
        }

        if ($response = $this->response) {
            foreach (Arr::get($response, 'data') as $data) {
                $animeInfo = Arr::get($data, 'node');
                $listStatus = Arr::get($data, 'list_status');

                $watchStatus = Arr::get($listStatus, 'is_rewatching')
                    ? 'rewatching'
                    : Arr::get($listStatus, 'status');

                $entries[] = [
                    ExternalResource::ATTRIBUTE_EXTERNAL_ID => Arr::get($animeInfo, 'id'),
                    ExternalEntry::ATTRIBUTE_SCORE => Arr::get($listStatus, 'score'),
                    ExternalEntry::ATTRIBUTE_WATCH_STATUS => ExternalEntryWatchStatus::getMalMapping($watchStatus)->value,
                    ExternalEntry::ATTRIBUTE_IS_FAVORITE => false,
                ];
            }
        }

        return $entries;
    }

    /**
     * Get the username.
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        if ($this->userResponse === null) {
            $this->makeUserRequest();
        }

        return Arr::get($this->userResponse, 'name');
    }

    /**
     * Get the id of the external user.
     *
     * @return int|null
     */
    public function getUserId(): ?int
    {
        if ($this->response === null) {
            $this->makeUserRequest();
        }

        return Arr::get($this->userResponse, 'id');
    }

    /**
     * Make the request to the user endpoint of the external api.
     *
     * @return static
     */
    protected function makeUserRequest(): static
    {
        try {
            $this->userResponse = Http::withToken($this->getToken())
                ->get('https://api.myanimelist.net/v2/users/@me')
                ->throw()
                ->json();

            return $this;
        } catch (RequestException $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }

    /**
     * Make the request to the external api.
     *
     * @return static
     */
    protected function makeRequest(): static
    {
        try {
            $this->response = Http::withToken($this->getToken())
                ->get('https://api.myanimelist.net/v2/users/@me/animelist', [
                    'fields' => 'list_status',
                    'limit' => '1000',
                ])
                ->throw()
                ->json();

            return $this;
        } catch (RequestException $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }
}
