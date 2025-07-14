<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External\Entry\Claimed;

use App\Actions\Models\List\External\Entry\BaseExternalEntryClaimedAction;
use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Enums\Models\List\ExternalProfileSite;
use App\Models\List\External\ExternalEntry;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Uri;

/**
 * Class MalExternalEntryClaimedAction.
 */
class MalExternalEntryClaimedAction extends BaseExternalEntryClaimedAction
{
    /**
     * The JSON response of the user endpoint in external API.
     *
     * @var array|null
     */
    protected ?array $userData = null;

    /**
     * Get the entries of the response.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getEntries(): array
    {
        $entries = [];

        if ($this->data === null) {
            $this->makeRequest();
        }

        if ($data = $this->data) {
            foreach ($data as $info) {
                $animeInfo = Arr::get($info, 'node');
                $listStatus = Arr::get($info, 'list_status');

                $watchStatus = Arr::get($listStatus, 'is_rewatching')
                    ? 'rewatching'
                    : Arr::get($listStatus, 'status');

                if ($watchStatus === null) {
                    var_dump($listStatus, $info);
                }

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
        if ($this->userData === null) {
            $this->makeUserRequest();
        }

        return Arr::get($this->userData, 'name');
    }

    /**
     * Get the id of the external user.
     *
     * @return int|null
     */
    public function getUserId(): ?int
    {
        if ($this->data === null) {
            $this->makeUserRequest();
        }

        return Arr::get($this->userData, 'id');
    }

    /**
     * Make the request to the user endpoint of the external api.
     *
     * @return void
     *
     * @throws RequestException
     */
    protected function makeUserRequest(): void
    {
        try {
            $this->userData = Http::withToken($this->getToken())
                ->get('https://api.myanimelist.net/v2/users/@me')
                ->throw()
                ->json();
        } catch (RequestException $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }

    /**
     * Make the request to the external api.
     *
     * @return void
     *
     * @throws RequestException
     */
    protected function makeRequest(): void
    {
        $next = null;

        try {
            do {
                $response = Http::withToken($this->getToken())
                    ->get($next ?? 'https://api.myanimelist.net/v2/users/@me/animelist?fields=list_status&limit=1000')
                    ->throw()
                    ->json();

                $this->data = array_merge($this->data ?? [], Arr::get($response, 'data'));

                $next = Arr::get($response, 'paging.next');

                // TODO: test
                RateLimiter::hit(ExternalProfileSite::MAL->name);

            } while (filled($next));

        } catch (RequestException $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }
}
