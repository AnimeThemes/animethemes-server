<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalEntry\Site;

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
        $response = $this->makeRequest();

        if ($response !== null) {
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
     * Make the request to the external api.
     *
     * @return array|null
     */
    public function makeRequest(): ?array
    {
        try {
            $query = '
                query($userName: String) {
                    MediaListCollection(userName: $userName, type: ANIME) {
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
                'userName' => $this->getUsername(),
            ];

            $response = Http::withToken($this->getToken())
                ->post('https://graphql.anilist.co', [
                    'query' => $query,
                    'variables' => $variables,
                ])
                ->throw()
                ->json();

            return $response;

        } catch (RequestException $e) {
            Log::error($e->getMessage());

            throw $e;
        }
    }
}
