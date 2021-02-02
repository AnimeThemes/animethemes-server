<?php

namespace Database\Seeders;

use App\Enums\AnimeSeason;
use App\Models\Anime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnimeSeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (WikiPages::YEAR_MAP as $year_page => $years) {

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Year page content
            $year_wiki_contents = file_get_contents($year_page);
            $year_wiki_json = json_decode($year_wiki_contents);
            $year_wiki_content_md = $year_wiki_json->data->content_md;

            // We want to proceed line by line
            preg_match_all('/^(.*)$/m', $year_wiki_content_md, $anime_season_wiki_entries, PREG_SET_ORDER);

            // The current year and season
            $year = $years[0];
            $season = null;

            foreach ($anime_season_wiki_entries as $anime_season_wiki_entry) {
                $wiki_entry_line = html_entity_decode($anime_season_wiki_entry[0]);

                // If Season heading line, set the current Season
                // Format: "##{Year} {Season} Season (Quarter)"
                if (preg_match('/^##(\d+).*(Fall|Summer|Spring|Winter).*(?:\\r)?$/', $wiki_entry_line, $anime_season)) {
                    $season = AnimeSeason::getValue(Str::upper($anime_season[2]));
                    continue;
                }

                // If Anime heading line, attempt to set Anime to current Season
                // Format: "###[{Anime Name}]({Resource Link})"
                if ($year !== null && $season !== null && preg_match('/###\[(.*)\]\(https\:\/\/.*\)/', $wiki_entry_line, $anime_name)) {
                    try {
                        // Set season if we have a definitive match
                        // This is not guaranteed as an Anime Name may be inconsistent between indices
                        $matching_anime = Anime::where('name', html_entity_decode($anime_name[1]))->where('year', $year);
                        if ($matching_anime->count() === 1) {
                            $anime = $matching_anime->first();
                            $anime->season = $season;
                            if ($anime->isDirty()) {
                                Log::info("Setting season '{$season}' for anime '{$anime->name}'");
                                $anime->save();
                            }
                        }
                    } catch (\Exception $exception) {
                        Log::error($exception);
                    }

                    continue;
                }

                // Otherwise just fall through to the next line
            }
        }
    }
}
