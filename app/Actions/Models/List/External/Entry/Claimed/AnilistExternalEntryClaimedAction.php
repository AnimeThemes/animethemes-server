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
 * Class AnilistExternalEntryClaimedAction.
 */
class AnilistExternalEntryClaimedAction extends BaseExternalEntryClaimedAction
{
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
            $lists = Arr::where(Arr::get($data, 'MediaListCollection.lists'), fn ($value) => $value['isCustomList'] === false);

            foreach ($lists as $list) {
                foreach (Arr::get($list, 'entries') as $entry) {
                    $entryId = intval(Arr::get($entry, 'media.id'));
                    $entries[] = [
                        ExternalResource::ATTRIBUTE_EXTERNAL_ID => $entryId,
                        ExternalEntry::ATTRIBUTE_SCORE => Arr::get($entry, 'score'),
                        ExternalEntry::ATTRIBUTE_WATCH_STATUS => ExternalEntryWatchStatus::getAnilistMapping(Arr::get($entry, 'status'))->value,
                        ExternalEntry::ATTRIBUTE_IS_FAVORITE => Arr::get($entry, 'media.isFavourite'),
                    ];
                }
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
        if ($this->data === null) {
            $this->makeRequest();
        }

        return Arr::get($this->data, 'Viewer.name');
    }

    /**
     * Get the id of the external user.
     *
     * @return int|null
     */
    public function getUserId(): ?int
    {
        if ($this->userId !== null) {
            return $this->userId;
        }

        [, $payload] = explode('.', $this->getToken());

        $decodedArray = json_decode(base64_decode($payload), true);

        $this->userId = intval(Arr::get($decodedArray, 'sub'));

        return $this->userId;
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
        try {
            $query = '
                query($userId: Int) {
                    Viewer {
                        name
                    }
                    MediaListCollection(userId: $userId, type: ANIME) {
                        lists {
                            name
                            status
                            isCustomList
                            entries {
                                status
                                score
                                media {
                                    id
                                    isFavourite
                                }
                            }
                        }
                    }
                }
            ';

            $variables = [
                'userId' => $this->getUserId(),
            ];

            $this->data = Http::withToken($this->getToken())
                ->post('https://graphql.anilist.co', [
                    'query' => $query,
                    'variables' => $variables,
                ])
                ->throw()
                ->json('data');
        } catch (RequestException $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }
}
