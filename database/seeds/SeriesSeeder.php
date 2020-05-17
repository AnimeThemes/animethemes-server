<?php

use App\Models\Anime;
use App\Models\Series;
use Illuminate\Database\Seeder;

class SeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Remove any existing rows in Series-related tables
        // We want these tables to match the subreddit wiki
        DB::table('anime_series')->delete();
        DB::table('series')->delete();

        // Get JSON of Series Index page content
        $series_wiki_contents = file_get_contents('https://old.reddit.com/r/AnimeThemes/wiki/series.json');
        $series_wiki_json = json_decode($series_wiki_contents);
        $series_wiki_content_md = $series_wiki_json->data->content_md;

        // Match Series Entries
        // Format: "[{Series Name}](/r/AnimeThemes/wiki/series/{Series Alias}/)"
        preg_match_all('/\[.*\]\(.*series.*\)/m', $series_wiki_content_md, $series_wiki_entries, PREG_PATTERN_ORDER);

        foreach ($series_wiki_entries[0] as $series_wiki_entry) {

            // Get Series Name, Series Alias, and Series Link from Entry
            preg_match('/\[(.*)\]/', $series_wiki_entry, $series_wiki_entry_name);
            preg_match('/\[.*]\(\/r\/AnimeThemes\/wiki\/series\/(.*)\)/', $series_wiki_entry, $series_wiki_entry_alias);
            preg_match('/\[.*]\((\/r\/AnimeThemes\/wiki\/series\/.*)\)/', $series_wiki_entry, $series_wiki_entry_link);

            // Create Model from subreddit Alias & Name
            $series = Series::create([
                'name' => $series_wiki_entry_name[1],
                'alias' => $series_wiki_entry_alias[1]
            ]);

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Series Entry page content
            $series_anime_wiki_contents = file_get_contents('https://old.reddit.com' . $series_wiki_entry_link[1] . '.json');
            $series_anime_wiki_json = json_decode($series_anime_wiki_contents);
            $series_anime_wiki_content_md = $series_anime_wiki_json->data->content_md;

            // Match headers of Anime that belong to the Series
            // Format: "###[{Anime Name}]({Resource Link})
            preg_match_all('/###\[(.*)\]\(.*\)/m', $series_anime_wiki_content_md, $series_anime_wiki_entries, PREG_PATTERN_ORDER);

            // Attach Anime to Series by Name
            $series_anime = Anime::whereIn('name', $series_anime_wiki_entries[1])->get();
            $series->anime()->sync($series_anime);
        }
    }
}
