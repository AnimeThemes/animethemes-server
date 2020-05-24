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
        // Format: "[{Series Name}](/r/AnimeThemes/wiki/series/{Series Alias}/)
        preg_match_all('/\[(.*)\]\((\/r\/AnimeThemes\/wiki\/series\/(.*))\)/m', $series_wiki_content_md, $series_wiki_entries, PREG_SET_ORDER);

        foreach ($series_wiki_entries as $series_wiki_entry) {
            $series_name = html_entity_decode($series_wiki_entry[1]);
            $series_link = 'https://old.reddit.com' . $series_wiki_entry[2] . '.json';
            $series_alias = $series_wiki_entry[3];

            // Create Model from subreddit Alias & Name
            $series = Series::create([
                'name' => $series_name,
                'alias' => $series_alias
            ]);

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Series Entry page content
            $series_anime_wiki_contents = file_get_contents($series_link);
            $series_anime_wiki_json = json_decode($series_anime_wiki_contents);
            $series_anime_wiki_content_md = $series_anime_wiki_json->data->content_md;

            // Match headers of Anime in Series Entry page
            // Format: "###[{Anime Name}]({Resource Link})"
            preg_match_all('/###\[(.*)\]\(https\:\/\/.*\)/m', $series_anime_wiki_content_md, $series_anime_wiki_entries, PREG_PATTERN_ORDER);
            $series_anime_names = array_map(function ($series_anime_wiki_entry) {
                return html_entity_decode($series_anime_wiki_entry);
            }, $series_anime_wiki_entries[1]);

            // Attach Anime to Series by Name
            // Note: We are not concerned about Name collision here. It's more likely that collisions are within a series.
            $anime_series = Anime::whereIn('name', $series_anime_names)->get();
            $series->anime()->sync($anime_series);
        }
    }
}
