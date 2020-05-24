<?php

use App\Enums\ThemeType;
use App\Models\Artist;
use App\Models\Anime;
use App\Models\Theme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ArtistSongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Remove any existing rows in Artist-Song pivot table
        // We want this table to match the subreddit wiki
        DB::table('artist_song')->delete();

        // Get JSON of Artist Index page content
        $artist_wiki_contents = file_get_contents('https://old.reddit.com/r/AnimeThemes/wiki/artist.json');
        $artist_wiki_json = json_decode($artist_wiki_contents);
        $artist_wiki_content_md = $artist_wiki_json->data->content_md;

        // Match Artist Entries
        // Format: "[{Artist Name}](/r/AnimeThemes/wiki/artist/{Artist Alias}/)
        preg_match_all('/\[(.*)\]\((\/r\/AnimeThemes\/wiki\/artist\/(.*))\)/m', $artist_wiki_content_md, $artist_wiki_entries, PREG_SET_ORDER);

        foreach ($artist_wiki_entries as $artist_wiki_entry) {
            $artist_name = html_entity_decode($artist_wiki_entry[1]);
            $artist_link = 'https://old.reddit.com' . $artist_wiki_entry[2] . '.json';
            $artist_alias = html_entity_decode($artist_wiki_entry[3]);

            $artist = NULL;

            try {
                $artist = Artist::where('name', $artist_name)->firstOrFail();
            } catch (Exception $exception) {
                LOG::error($exception);
                continue;
            }

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Artist Entry page content
            $artist_song_wiki_contents = file_get_contents($artist_link);
            $artist_song_wiki_json = json_decode($artist_song_wiki_contents);
            $artist_song_wiki_content_md = $artist_song_wiki_json->data->content_md;

            // We want to proceed line by line
            preg_match_all('/^(.*)$/m', $artist_song_wiki_content_md, $artist_song_wiki_entries, PREG_SET_ORDER);

            // The current Anime
            $anime = NULL;

            foreach ($artist_song_wiki_entries as $artist_song_wiki_entry) {
                $wiki_entry_line = html_entity_decode($artist_song_wiki_entry[0]);

                // If Anime heading line, attempt to set current Anime
                if (preg_match('/^###\[(.*)\]\(https\:\/\/.*\)(?:\\r)?$/', $wiki_entry_line, $anime_name)) {
                    try {
                        // Set current Anime if we have a definitive match
                        // This is not guaranteed as an Anime Name may be inconsistent between indices
                        $matching_anime = Anime::where('name', html_entity_decode($anime_name[1]));
                        if ($matching_anime->count() === 1) {
                            $anime = $matching_anime->first();
                            continue;
                        }
                    } catch (Exception $exception) {
                        LOG::error($exception);
                    }

                    $anime = NULL;
                    continue;
                }

                // If Theme line, attempt to load Theme and associate Song to Artist
                if (!is_null($anime) && preg_match('/^(OP|ED)(\d*)(?:\sV(\d*))?.*\"(.*)\"(?:\sby\s(.*))?\|\[Webm.*\]\(https\:\/\/animethemes\.moe\/video\/(.*)\)\|(.*)\|(.*)(?:\\r)?$/', $wiki_entry_line, $theme_match)) {
                    LOG::info($theme_match);
                    $theme_type = $theme_match[1];
                    $sequence = $theme_match[2];
                    $version = $theme_match[3];

                    // Create Theme if no version or V1
                    if (!is_numeric($version) || intval($version) === 1) {

                        // Load Song through Theme
                        $query = Theme::query();
                        $query = $query->where('anime_id', $anime->anime_id)
                            ->where('type', ThemeType::getValue(strtoupper($theme_type)));
                        if (is_numeric($sequence)) {
                            $query = $query->where('sequence', intval($sequence));
                        } else {
                            $query = $query->whereNull('sequence');
                        }

                        try {
                            $matching_themes = $query->get();
                            if ($matching_themes->count() === 1) {
                                $theme = $matching_themes->first();

                                $song = $theme->song;

                                $artist->songs()->attach($song);
                            }
                        } catch (Exception $exception) {
                            LOG::error($exception);
                        }
                    }
                }
            }
        }
    }
}
