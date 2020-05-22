<?php

use App\Models\Anime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AnimeThemeSeeder extends Seeder
{

    // Hard-coded addresses of year pages
    // I don't really care about making this more elegant
    const YEAR_PAGES = [
        'https://www.reddit.com/r/AnimeThemes/wiki/60s.json',
        /*'https://www.reddit.com/r/AnimeThemes/wiki/70s.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/80s.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/90s.json',
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
        'https://www.reddit.com/r/AnimeThemes/wiki/2020.json',*/
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (AnimeThemeSeeder::YEAR_PAGES as $year_page) {

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Year page content
            $year_wiki_contents = file_get_contents($year_page);
            $year_wiki_json = json_decode($year_wiki_contents);
            $year_wiki_content_md = $year_wiki_json->data->content_md;

            // We want to proceed line by line
            preg_match_all('/^(.*)$/m', $year_wiki_content_md, $anime_theme_wiki_entries, PREG_SET_ORDER);

            // The current Anime & Group
            $anime = NULL;
            $group = NULL;

            foreach ($anime_theme_wiki_entries as $anime_theme_wiki_entry) {
                $wiki_entry_line = html_entity_decode($anime_theme_wiki_entry[0]);

                // If Anime heading line, attempt to set current Anime
                if (preg_match('/###\[(.*)\]\(https\:\/\/.*\)/', $wiki_entry_line, $anime_name)) {
                    try {
                        // Set current Anime if we have a definitive match
                        // This is not guaranteed as an Anime Name may be inconsistent between indices
                        $matching_anime = Anime::where('name', html_entity_decode($anime_name[1]));
                        if ($matching_anime->count() === 1) {
                            $anime = $matching_anime->first();
                            $group = NULL;
                            continue;
                        }
                    } catch (Exception $exception) {
                        LOG::error($exception);
                    }

                    $anime = NULL;
                    $group = NULL;
                    continue;
                }

                // If Synonym heading line, attempt to set synonyms
                if (preg_match('/^\*\*(.*)\*\*/', $wiki_entry_line, $synonyms)) {
                    if (!is_null($anime)) {
                        $synonym_list = explode(', ', html_entity_decode($synonyms[1]));
                        foreach ($synonym_list as $synonym) {
                            $anime->synonyms()->create([
                                'text' => $synonym
                            ]);
                        }
                    }
                }

                //If Theme line, attempt to create theme
            }
        }
    }
}
