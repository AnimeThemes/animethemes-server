<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalEntry\Token;

use App\Actions\Models\List\ExternalProfile\ExternalEntry\BaseExternalEntryTokenAction;
use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Models\List\External\ExternalEntry;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class AnilistExternalEntryTokenAction.
 */
class AnilistExternalEntryTokenAction extends BaseExternalEntryTokenAction
{
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
            $lists = Arr::where(Arr::get($response, 'data.MediaListCollection.lists'), fn ($value) => $value['isCustomList'] === false);

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
        if ($this->response === null) {
            $this->makeRequest();
        }

        return Arr::get($this->response, 'data.Viewer.name');
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
     * @return static
     *
     * @throws RequestException
     */
    protected function makeRequest(): static
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
                                private
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

            $this->response = Http::withToken($this->getToken())
                ->post('https://graphql.anilist.co', [
                    'query' => $query,
                    'variables' => $variables,
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
