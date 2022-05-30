<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class AniDbResourceSeeder.
 */
class AniDbResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Get anime that have MAL resource but do not have AniDB resource
        $animes = Anime::query()
            ->select([Anime::ATTRIBUTE_ID, Anime::ATTRIBUTE_NAME])
            ->whereHas(Anime::RELATION_RESOURCES, function (Builder $resourceQuery) {
                $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
            })->whereDoesntHave(Anime::RELATION_RESOURCES, function (Builder $resourceQuery) {
                $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB);
            })
            ->get();

        foreach ($animes as $anime) {
            if (! $anime instanceof Anime) {
                continue;
            }

            $malResource = $anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
            if ($malResource instanceof ExternalResource && $malResource->external_id !== null) {
                // Try not to upset Yuna
                sleep(rand(2, 5));

                // Yuna api call
                try {
                    $response = Http::get('https://relations.yuna.moe/api/ids', [
                        'source' => 'myanimelist',
                        'id' => $malResource->external_id,
                    ])
                    ->throw()
                    ->json();

                    $anidbId = Arr::get($response, 'anidb');

                    // Only proceed if we have a match
                    if ($anidbId !== null) {
                        // Check if AniDB resource already exists
                        $anidbResource = ExternalResource::query()
                            ->select([ExternalResource::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_LINK])
                            ->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB)
                            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $anidbId)
                            ->first();

                        // Create AniDB resource if it doesn't already exist
                        if ($anidbResource === null) {
                            Log::info("Creating anidb resource '$anidbId' for anime '$anime->name'");

                            $anidbResource = ExternalResource::factory()->createOne([
                                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $anidbId,
                                ExternalResource::ATTRIBUTE_LINK => "https://anidb.net/anime/$anidbId",
                                ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANIDB,
                            ]);
                        }

                        // Attach AniDB resource to anime
                        if (AnimeResource::query()
                            ->where($anime->getKeyName(), $anime->getKey())
                            ->where($anidbResource->getKeyName(), $anidbResource->getKey())
                            ->doesntExist()
                        ) {
                            Log::info("Attaching resource '$anidbResource->link' to anime '$anime->name'");
                            $anidbResource->anime()->attach($anime);
                        }
                    }
                } catch (RequestException $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }
}
