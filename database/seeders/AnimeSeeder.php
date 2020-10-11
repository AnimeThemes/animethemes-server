<?php

namespace Database\Seeders;

use App\Models\Anime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Remove any existing rows in Anime-related tables
        // We want these tables to match the subreddit wiki
        DB::table('entry_video')->delete();
        DB::table('entry')->delete();
        DB::table('theme')->delete();
        DB::table('synonym')->delete();
        DB::table('anime_resource')->delete();
        DB::table('anime_series')->delete();
        DB::table('anime')->delete();

        // Get JSON of Anime Index page content
        $anime_wiki_contents = file_get_contents('https://old.reddit.com/r/AnimeThemes/wiki/anime_index.json');
        $anime_wiki_json = json_decode($anime_wiki_contents);
        $anime_wiki_content_md = $anime_wiki_json->data->content_md;

        // Match Anime Entries
        // Format: "[{Anime Name} ({Year})](/r/AnimeThemes/wiki/{year}#{anchor link})"
        preg_match_all('/\[(.*)\s\((.*)\)\]\(\/r\/AnimeThemes\/wiki\/.*\)/m', $anime_wiki_content_md, $anime_wiki_entries, PREG_SET_ORDER);

        $slugs = [];

        foreach ($anime_wiki_entries as $anime_wiki_entry) {
            $anime_name = html_entity_decode($anime_wiki_entry[1]);
            $anime_year = $anime_wiki_entry[2];

            // Generate default slug
            // Fallback: append year if first attempt exists
            $slug = Str::slug($anime_name, '_');
            if (in_array($slug, $slugs)) {
                $slug = Str::slug($anime_name.' '.$anime_year, '_');
            }
            $slugs[] = $slug;

            // Year expects a number but we group 60s, 70s, 80s & 90s
            // Fallback: Change group values to 1960, 1970, 1980 & 1990 for later inspection
            if (strpos($anime_year, 's') !== false) {
                $anime_year = '19'.str_replace('s', '', $anime_year);
            }

            // Create Model from subreddit Name and Year and generated slug
            Anime::create([
                'name' => $anime_name,
                'alias' => $slug,
                'year' => $anime_year,
            ]);
        }
    }
}
