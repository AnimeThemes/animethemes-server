<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\AnimeSeries;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Class SeriesSeeder.
 */
class SeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Get JSON of Series Index page content
        $seriesWikiContents = WikiPages::getPageContents(WikiPages::SERIES_INDEX);
        if ($seriesWikiContents === null) {
            return;
        }

        // Match Series Entries
        // Format: "[{Series Name}](/r/AnimeThemes/wiki/series/{Series Slug}/)
        preg_match_all(
            '/\[(.*)]\(\/r\/AnimeThemes\/wiki\/series\/(.*)\)/m',
            $seriesWikiContents,
            $seriesWikiEntries,
            PREG_SET_ORDER
        );

        foreach ($seriesWikiEntries as $seriesWikiEntry) {
            $seriesName = html_entity_decode($seriesWikiEntry[1]);
            $seriesSlug = $seriesWikiEntry[2];

            // Create series if it doesn't already exist
            $series = Series::query()
                ->select([Series::ATTRIBUTE_ID, Series::ATTRIBUTE_NAME])
                ->where(Series::ATTRIBUTE_NAME, $seriesName)
                ->where(Series::ATTRIBUTE_SLUG, $seriesSlug)
                ->first();

            if ($series === null) {
                Log::info("Creating series with name '$seriesName' and slug '$seriesSlug'");

                $series = Series::factory()->createOne([
                    Series::ATTRIBUTE_NAME => $seriesName,
                    Series::ATTRIBUTE_SLUG => $seriesSlug,
                ]);
            }

            // Try not to upset Reddit
            sleep(rand(2, 5));

            // Get JSON of Series Entry page content
            $seriesLink = WikiPages::getSeriesPage($seriesSlug);
            $seriesAnimeWikiContents = WikiPages::getPageContents($seriesLink);
            if ($seriesAnimeWikiContents === null) {
                continue;
            }

            // Match headers of Anime in Series Entry page
            // Format: "###[{Anime Name}]({Resource Link})"
            preg_match_all(
                '/###\[(.*)]\(https:\/\/.*\)/m',
                $seriesAnimeWikiContents,
                $seriesAnimeWikiEntries,
                PREG_PATTERN_ORDER
            );

            $seriesAnimeNames = array_map(
                fn (string $seriesAnimeWikiEntry) => html_entity_decode($seriesAnimeWikiEntry),
                $seriesAnimeWikiEntries[1]
            );

            // Attach Anime to Series by Name
            // Note: We are not concerned about Name collision here.
            // It's more likely that collisions occur within a series.
            $animes = Anime::query()
                ->select([Anime::ATTRIBUTE_ID, Anime::ATTRIBUTE_NAME])
                ->whereIn(Anime::ATTRIBUTE_NAME, $seriesAnimeNames)
                ->get();

            foreach ($animes as $anime) {
                if ($anime instanceof Anime
                    && AnimeSeries::query()
                        ->where($anime->getKeyName(), $anime->getKey())
                        ->where($series->getKeyName(), $series->getKey())
                        ->doesntExist()
                ) {
                    Log::info("Attaching anime '$anime->name' to series '$series->name'");
                    $series->anime()->attach($anime);
                }
            }
        }
    }
}
