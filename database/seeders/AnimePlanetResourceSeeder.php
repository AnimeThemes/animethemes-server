<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\ExternalResource;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonMachine\Exception\InvalidArgumentException;
use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;

/**
 * Class AnimePlanetResourceSeeder.
 */
class AnimePlanetResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function run(): void
    {
        $path = storage_path('app/anime-offline-database.json');

        $data = Items::fromFile($path, [
            'decoder' => new ExtJsonDecoder(true),
            'pointer' => '/data',
        ]);

        foreach ($data as $animeData) {
            $anime = $this->getAnime($animeData);
            if ($anime === null) {
                continue;
            }

            $animePlanetResource = $anime->resources->firstWhere(fn (ExternalResource $resource) => ResourceSite::ANIME_PLANET()->is($resource->site));
            if ($animePlanetResource !== null) {
                continue;
            }

            $animePlanetLinks = $this->getAnimePlanetSources($animeData);
            foreach ($animePlanetLinks as $animePlanetLink) {
                $resource = $this->getOrCreateResource($animePlanetLink);

                Log::info("Attaching Resource '{$resource->getName()}' to Anime '{$anime->getName()}'");
                $anime->resources()->attach($resource);
            }
        }
    }

    /**
     * Resolve anime from data entry.
     * Here we will funnel query criteria to account for year and season mismatches.
     *
     * @param  array  $animeData
     * @return Anime|null
     */
    protected function getAnime(array $animeData): ?Anime
    {
        $builder = Anime::query();

        $builder = $builder->with(Anime::RELATION_RESOURCES);

        $title = Arr::get($animeData, 'title');
        $synonyms = Arr::get($animeData, 'synonyms');
        if (is_string($title) && is_array($synonyms)) {
            $builder->where(function (Builder $query) use ($title, $synonyms) {
                $query->orWhere(Anime::ATTRIBUTE_NAME, $title)
                    ->orWhereHas(
                        Anime::RELATION_SYNONYMS,
                        fn (Builder $synonymBuilder) => $synonymBuilder->whereIn(AnimeSynonym::ATTRIBUTE_TEXT, $synonyms)
                    );
            });
        }

        $anime = $builder->get();
        if ($anime->containsOneItem()) {
            return $anime->first();
        }

        $year = Arr::get($animeData, 'animeSeason.year');
        if (is_numeric($year)) {
            $builder = $builder->where(Anime::ATTRIBUTE_YEAR, $year);
        }

        $anime = $builder->get();
        if ($anime->containsOneItem()) {
            return $anime->first();
        }

        try {
            $season = AnimeSeason::getValue(Arr::get($animeData, 'animeSeason.season'));
            if (is_numeric($season)) {
                $builder = $builder->where(Anime::ATTRIBUTE_SEASON, $season);
            }
        } catch (Exception) {
            $season = Arr::get($animeData, 'animeSeason.season');
            Log::error("Invalid season key $season");
        }

        $anime = $builder->get();
        if ($anime->containsOneItem()) {
            return $anime->first();
        }

        return null;
    }

    /**
     * Get the Anime Planet source links.
     *
     * @param  array  $animeData
     * @return array
     */
    protected function getAnimePlanetSources(array $animeData): array
    {
        $sources = Arr::get($animeData, 'sources');

        if (is_array($sources)) {
            return Arr::where($sources, fn (string $link) => ResourceSite::ANIME_PLANET()->is(ResourceSite::valueOf($link)));
        }

        return [];
    }

    /**
     * Get or Create Resource from link.
     *
     * @param  string  $animePlanetLink
     * @return ExternalResource
     */
    protected function getOrCreateResource(string $animePlanetLink): ExternalResource
    {
        $resource = ExternalResource::query()
            ->where(ExternalResource::ATTRIBUTE_LINK, $animePlanetLink)
            ->first();

        if ($resource == null) {
            $site = ResourceSite::ANIME_PLANET();

            Log::info("Creating $site->description Resource '$animePlanetLink'");

            $resource = ExternalResource::query()->create([
                ExternalResource::ATTRIBUTE_LINK => $animePlanetLink,
                ExternalResource::ATTRIBUTE_SITE => $site->value,
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $this->getAnimePlanetExternalId($animePlanetLink),
            ]);
        }

        return $resource;
    }

    /**
     * Attempt to retrieve Anime Planet ID from webpage.
     *
     * @param string $animePlanetLink
     * @return int|null
     */
    protected function getAnimePlanetExternalId(string $animePlanetLink): ?int
    {
        try {
            // Try not to upset Anime Planet
            sleep(rand(1, 3));

            $response = Http::get($animePlanetLink)
                ->throw()
                ->body();

            $animePlanetId = Str::match(
                '/["\']?ENTRY_INFO["\']? *: *{.*id["\']? *: *["\']?(\d+)["\']? *,/s',
                $response
            );

            if (is_numeric($animePlanetId)) {
                return intval($animePlanetId);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }
}
