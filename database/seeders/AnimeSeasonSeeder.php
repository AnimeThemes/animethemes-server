<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AnimeSeason;
use App\Models\Anime;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class AnimeSeasonSeeder
 * @package Database\Seeders
 */
class AnimeSeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (WikiPages::YEAR_MAP as $yearPage => $years) {

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Year page content
            $yearWikiContents = WikiPages::getPageContents($yearPage);
            if ($yearWikiContents === null) {
                continue;
            }

            // We want to proceed line by line
            preg_match_all('/^(.*)$/m', $yearWikiContents, $animeSeasonWikiEntries, PREG_SET_ORDER);

            // The current year and season
            $year = $years[0];
            $season = null;

            foreach ($animeSeasonWikiEntries as $animeSeasonWikiEntry) {
                $wikiEntryLine = html_entity_decode($animeSeasonWikiEntry[0]);

                // If Season heading line, set the current Season
                // Format: "##{Year} {Season} Season (Quarter)"
                if (preg_match('/^##(\d+).*(Fall|Summer|Spring|Winter).*(?:\\r)?$/', $wikiEntryLine, $animeSeason)) {
                    $season = AnimeSeason::getValue(Str::upper($animeSeason[2]));
                    continue;
                }

                // If Anime heading line, attempt to set Anime to current Season
                // Format: "###[{Anime Name}]({Resource Link})"
                if ($year !== null && $season !== null && preg_match('/###\[(.*)\]\(https\:\/\/.*\)/', $wikiEntryLine, $animeName)) {
                    try {
                        // Set season if we have a definitive match
                        // This is not guaranteed as an Anime Name may be inconsistent between indices
                        $matchingAnime = Anime::where('name', html_entity_decode($animeName[1]))->where('year', $year);
                        if ($matchingAnime->count() === 1) {
                            $anime = $matchingAnime->first();
                            $anime->season = $season;
                            if ($anime->isDirty()) {
                                Log::info("Setting season '{$season}' for anime '{$anime->name}'");
                                $anime->save();
                            }
                        }
                    } catch (Exception $e) {
                        Log::error($e->getMessage());
                    }

                }

                // Otherwise just fall through to the next line
            }
        }
    }
}
