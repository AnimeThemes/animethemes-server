<?php

namespace Database\Seeders;

use App\Models\Anime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
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
        // Get JSON of Anime Index page content
        $anime_wiki_contents = WikiPages::getPageContents(WikiPages::ANIME_INDEX);
        if ($anime_wiki_contents === null) {
            return;
        }

        // Match Anime Entries
        // Format: "[{Anime Name} ({Year})](/r/AnimeThemes/wiki/{year}#{anchor link})"
        preg_match_all('/\[(.*)\s\((.*)\)\]\(\/r\/AnimeThemes\/wiki\/.*\)/m', $anime_wiki_contents, $anime_wiki_entries, PREG_SET_ORDER);

        foreach ($anime_wiki_entries as $anime_wiki_entry) {
            $anime_name = html_entity_decode($anime_wiki_entry[1]);
            $anime_year = $anime_wiki_entry[2];

            // Generate default slug
            // Fallback: append year if first attempt exists
            $slug = Str::slug($anime_name, '_');
            if (Anime::where('slug', $slug)->exists()) {
                $slug = Str::slug($anime_name.' '.$anime_year, '_');
            }

            // Year expects a number but we group 60s, 70s, 80s & 90s
            // Fallback: Change group values to 1960, 1970, 1980 & 1990 for later inspection
            $anime_years = WikiPages::getAnimeIndexYears($anime_year);

            // Create Anime if it doesn't already exist
            if (Anime::where('name', $anime_name)->whereIn('year', $anime_years)->doesntExist()) {
                Log::info("Creating anime '{$anime_name}'");
                Anime::create(
                    [
                        'name' => $anime_name,
                        'slug' => $slug,
                        'year' => $anime_years[0],
                    ]
                );
            }
        }
    }
}
