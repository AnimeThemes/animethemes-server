<?php

namespace Database\Seeders;

use App\Enums\ResourceSite;
use App\Models\Anime;
use App\Models\ExternalResource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnimeResourceSeeder extends Seeder
{
    // Hard-coded addresses of year pages
    // I don't really care about making this more elegant
    const YEAR_PAGES = [
        'https://www.reddit.com/r/AnimeThemes/wiki/60s.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/70s.json',
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
        'https://www.reddit.com/r/AnimeThemes/wiki/2020.json',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Remove any existing rows in Anime Resource pivot table
        // We want this table to match the subreddit wiki
        DB::table('anime_resource')->delete();

        foreach (AnimeResourceSeeder::YEAR_PAGES as $year_page) {

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Year page content
            $year_wiki_contents = file_get_contents($year_page);
            $year_wiki_json = json_decode($year_wiki_contents);
            $year_wiki_content_md = $year_wiki_json->data->content_md;

            // Match headers of Anime and links of Resources
            // Format: "###[{Anime Name}]({Resource Link})"
            preg_match_all('/###\[(.*)\]\((https\:\/\/.*)\)/m', $year_wiki_content_md, $anime_resource_wiki_entries, PREG_SET_ORDER);

            foreach ($anime_resource_wiki_entries as $anime_resource_wiki_entry) {
                $anime_name = html_entity_decode($anime_resource_wiki_entry[1]);
                $resource_link = html_entity_decode($anime_resource_wiki_entry[2]);

                // Create Resource Model with link and derived site
                $resource = ExternalResource::create([
                    'site' => ResourceSite::valueOf($resource_link),
                    'link' => $resource_link,
                ]);

                try {
                    // Attach Anime to Resource by Name if we have a definitive match
                    // This is not guaranteed as an Anime Name may be inconsistent between indices
                    $resource_anime = Anime::where('name', $anime_name)->get();
                    if ($resource_anime->count() === 1) {
                        $resource->anime()->attach($resource_anime);
                    }
                } catch (\Exception $exception) {
                    LOG::error($exception->getMessage());
                }
            }
        }
    }
}
