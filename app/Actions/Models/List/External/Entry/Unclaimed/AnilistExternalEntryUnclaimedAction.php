<?php

declare(strict_types=1);

namespace App\Actions\Models\List\External\Entry\Unclaimed;

use App\Actions\Models\List\External\Entry\BaseExternalEntryUnclaimedAction;
use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Models\List\External\ExternalEntry;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnilistExternalEntryUnclaimedAction extends BaseExternalEntryUnclaimedAction
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

        $favorites = Arr::map(Arr::get($this->data, 'User.favourites.anime.nodes'), fn ($value) => $value['id']);
        $lists = Arr::where(Arr::get($this->data, 'MediaListCollection.lists'), fn ($value): bool => $value['isCustomList'] === false);

        foreach ($lists as $list) {
            foreach (Arr::get($list, 'entries') as $entry) {
                $entryId = intval(Arr::get($entry, 'media.id'));
                $entries[] = [
                    ExternalResource::ATTRIBUTE_EXTERNAL_ID => $entryId,
                    ExternalEntry::ATTRIBUTE_SCORE => Arr::get($entry, 'score'),
                    ExternalEntry::ATTRIBUTE_WATCH_STATUS => ExternalEntryWatchStatus::getAnilistMapping(Arr::get($entry, 'status'))->value,
                    ExternalEntry::ATTRIBUTE_IS_FAVORITE => in_array($entryId, $favorites),
                ];
            }
        }

        return $entries;
    }

    public function getUserId(): ?int
    {
        if ($this->data === null) {
            $this->makeRequest();
        }

        return Arr::get($this->data, 'User.id');
    }

    /**
     * @throws RequestException
     */
    protected function makeRequest(): void
    {
        try {
            $query = '
                query($userName: String) {
                    User(name: $userName) {
                        id
                        favourites {
                            anime {
                                nodes {
                                    id
                                }
                            }
                        }
                    }
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

            $this->data = Http::post('https://graphql.anilist.co', [
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
