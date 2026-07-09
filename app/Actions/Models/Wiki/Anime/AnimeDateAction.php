<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime;

use App\Actions\ActionResult;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class AnimeDateAction
{
    /**
     * @param  Collection<int, Anime>  $anime
     */
    public function handle(Collection $anime): ActionResult
    {
        $anilistIds = $anime
            ->map(fn (Anime $anime) => $anime->resources->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST->value)->first()?->external_id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $query = <<<'GRAPHQL'
            query AnimeDates($ids: [Int]) {
                Page(perPage: 20) {
                    media(id_in: $ids, type: ANIME) {
                        id
                        startDate {
                            year
                            month
                            day
                        }
                        endDate {
                            year
                            month
                            day
                        }
                    }
                }
            }
        GRAPHQL;

        $response = Http::post('https://graphql.anilist.co', [
            'query' => $query,
            'variables' => [
                'ids' => $anilistIds,
            ],
        ])
            ->throw();

        if (! $response->ok() || $response->json('errors')) {
            return new ActionResult(ActionStatus::FAILED, $response->json('errors.0.message'));
        }

        foreach ($response->json('data.Page.media') as $media) {
            $animeToUpdate = $anime->first(
                fn (Anime $anime): bool => $anime->resources->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST->value)->first()?->external_id === Arr::integer($media, 'id')
            );

            $animeToUpdate?->update([
                'start_date' => Arr::get($media, 'startDate'),
                'end_date' => Arr::get($media, 'endDate'),
            ]);
        }

        return new ActionResult(ActionStatus::PASSED);
    }
}
