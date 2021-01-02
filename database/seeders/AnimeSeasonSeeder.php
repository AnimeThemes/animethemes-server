<?php

namespace Database\Seeders;

use App\Enums\AnimeSeason;
use App\Models\Anime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnimeSeasonSeeder extends Seeder
{
    // Hard-coded addresses of year pages
    // I don't really care about making this more elegant
    const YEAR_PAGES = [
        'https://www.reddit.com/r/AnimeThemes/wiki/2000.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2001.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2002.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2003.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2004.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2005.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2006.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2007.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2008.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2009.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2010.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2011.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2012.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2013.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2014.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2015.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2016.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2017.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2018.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2019.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2020.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2021.json',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (AnimeSeasonSeeder::YEAR_PAGES as $year_page) {

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Year page content
            $year_wiki_contents = file_get_contents($year_page);
            $year_wiki_json = json_decode($year_wiki_contents);
            $year_wiki_content_md = $year_wiki_json->data->content_md;

            // We want to proceed line by line
            preg_match_all('/^(.*)$/m', $year_wiki_content_md, $anime_season_wiki_entries, PREG_SET_ORDER);

            // The current year and season
            $year = null;
            $season = null;

            foreach ($anime_season_wiki_entries as $anime_season_wiki_entry) {
                $wiki_entry_line = html_entity_decode($anime_season_wiki_entry[0]);

                // If Season heading line, set the current Season
                // Format: "##{Year} {Season} Season (Quarter)"
                if (preg_match('/^##(\d+).*(Fall|Summer|Spring|Winter).*(?:\\r)?$/', $wiki_entry_line, $anime_season)) {
                    $year = intval($anime_season[1]);
                    $season = AnimeSeason::getValue(Str::upper($anime_season[2]));
                    continue;
                }

                // If Anime heading line, attempt to set Anime to current Season
                // Format: "###[{Anime Name}]({Resource Link})"
                if (preg_match('/###\[(.*)\]\(https\:\/\/.*\)/', $wiki_entry_line, $anime_name)) {
                    try {
                        // Set season if we have a definitive match
                        // This is not guaranteed as an Anime Name may be inconsistent between indices
                        $matching_anime = Anime::where('name', html_entity_decode($anime_name[1]))->where('year', $year);
                        if ($matching_anime->count() === 1 && ! is_null($season)) {
                            $anime = $matching_anime->first();
                            $anime->season = $season;
                            if ($anime->isDirty()) {
                                $anime->save();
                            }
                        }
                    } catch (\Exception $exception) {
                        LOG::error($exception);
                    }

                    continue;
                }

                // Otherwise just fall through to the next line
            }
        }
    }
}
