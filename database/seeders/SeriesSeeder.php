<?php

namespace Database\Seeders;

use App\Models\Anime;
use App\Models\Series;
use App\Pivots\AnimeSeries;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get JSON of Series Index page content
        $series_wiki_contents = WikiPages::getPageContents(WikiPages::SERIES_INDEX);
        if ($series_wiki_contents === null) {
            return;
        }

        // Match Series Entries
        // Format: "[{Series Name}](/r/AnimeThemes/wiki/series/{Series Slug}/)
        preg_match_all('/\[(.*)\]\(\/r\/AnimeThemes\/wiki\/series\/(.*)\)/m', $series_wiki_contents, $series_wiki_entries, PREG_SET_ORDER);

        foreach ($series_wiki_entries as $series_wiki_entry) {
            $series_name = html_entity_decode($series_wiki_entry[1]);
            $series_slug = $series_wiki_entry[2];

            // Create series if it doesn't already exist
            $series = Series::where('name', $series_name)->where('slug', $series_slug)->first();
            if ($series === null) {
                Log::info("Creating series with name '{$series_name}' and slug '{$series_slug}'");
                $series = Series::create([
                    'name' => $series_name,
                    'slug' => $series_slug,
                ]);
            }

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Series Entry page content
            $series_link = WikiPages::getSeriesPage($series_slug);
            $series_anime_wiki_contents = WikiPages::getPageContents($series_link);
            if ($series_anime_wiki_contents === null) {
                continue;
            }

            // Match headers of Anime in Series Entry page
            // Format: "###[{Anime Name}]({Resource Link})"
            preg_match_all('/###\[(.*)\]\(https\:\/\/.*\)/m', $series_anime_wiki_contents, $series_anime_wiki_entries, PREG_PATTERN_ORDER);
            $series_anime_names = array_map(function ($series_anime_wiki_entry) {
                return html_entity_decode($series_anime_wiki_entry);
            }, $series_anime_wiki_entries[1]);

            // Attach Anime to Series by Name
            // Note: We are not concerned about Name collision here. It's more likely that collisions are within a series.
            $animes = Anime::whereIn('name', $series_anime_names)->get();
            foreach ($animes as $anime) {
                if (AnimeSeries::where($anime->getKeyName(), $anime->getKey())->where($series->getKeyName(), $series->getKey())->doesntExist()) {
                    Log::info("Attaching anime '{$anime->name}' to series '{$series->name}'");
                    $series->anime()->attach($anime);
                }
            }
        }
    }
}
