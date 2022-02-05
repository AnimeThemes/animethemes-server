<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Wiki\Anime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class AnimeSeeder.
 */
class AnimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Get JSON of Anime Index page content
        $animeWikiContents = WikiPages::getPageContents(WikiPages::ANIME_INDEX);
        if ($animeWikiContents === null) {
            return;
        }

        // Match Anime Entries
        // Format: "[{Anime Name} ({Year})](/r/AnimeThemes/wiki/{year}#{anchor link})"
        preg_match_all(
            '/\[(.*)\s\((.*)\)]\(\/r\/AnimeThemes\/wiki\/.*\)/m',
            $animeWikiContents,
            $animeWikiEntries,
            PREG_SET_ORDER
        );

        foreach ($animeWikiEntries as $animeWikiEntry) {
            $animeName = html_entity_decode($animeWikiEntry[1]);
            $animeYear = $animeWikiEntry[2];

            // Generate default slug
            // Fallback: append year if first attempt exists
            $slug = Str::slug($animeName, '_');
            if (Anime::query()->where(Anime::ATTRIBUTE_SLUG, $slug)->exists()) {
                $slug = Str::slug($animeName.' '.$animeYear, '_');
            }

            // Year expects a number, but we group 60s, 70s, 80s & 90s
            // Fallback: Change group values to 1960, 1970, 1980 & 1990 for later inspection
            $animeYears = WikiPages::getAnimeIndexYears($animeYear);

            // Create Anime if it doesn't already exist
            if (
                Anime::query()
                    ->where(Anime::ATTRIBUTE_NAME, $animeName)
                    ->whereIn(Anime::ATTRIBUTE_YEAR, $animeYears)
                    ->doesntExist()
            ) {
                Log::info("Creating anime '$animeName'");

                Anime::factory()->createOne([
                    Anime::ATTRIBUTE_NAME => $animeName,
                    Anime::ATTRIBUTE_SEASON => null,
                    Anime::ATTRIBUTE_SLUG => $slug,
                    Anime::ATTRIBUTE_SYNOPSIS => null,
                    Anime::ATTRIBUTE_YEAR => $animeYears[0],
                ]);
            }
        }
    }
}
