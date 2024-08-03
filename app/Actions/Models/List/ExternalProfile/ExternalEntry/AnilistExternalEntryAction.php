<?php

declare(strict_types=1);

namespace App\Actions\Models\List\ExternalProfile\ExternalEntry;

use App\Actions\Models\List\ExternalProfile\ExternalEntryAction;
use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Models\List\External\ExternalEntry;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class AnilistExternalEntryAction.
 */
class AnilistExternalEntryAction extends ExternalEntryAction
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
            $lists = Arr::get($response, 'data.MediaListCollection.lists');
            $lists = Arr::where($lists, fn ($value, $key) => $value['isCustomList'] === false);

            foreach ($lists as $list) {
                foreach (Arr::get($list, 'entries') as $entry) {
                    $entries[] = [
                        ExternalResource::ATTRIBUTE_EXTERNAL_ID => intval(Arr::get($entry, 'media.id')),
                        ExternalEntry::ATTRIBUTE_SCORE => Arr::get($entry, 'score'),
                        ExternalEntry::ATTRIBUTE_WATCH_STATUS => ExternalEntryWatchStatus::getAnilistMapping(Arr::get($entry, 'status'))->value,
                        ExternalEntry::ATTRIBUTE_IS_FAVORITE => false, // TODO
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
                                status
                                score
                                media {
                                    id
                                }
                            }
                        }
                    }
                }
            ';

            $variables = [
                'userName' => $this->getUsername(),
            ];

            $response = Http::post('https://graphql.anilist.co', [
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

        return null;
    }
}
