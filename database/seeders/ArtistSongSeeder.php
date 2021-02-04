<?php

namespace Database\Seeders;

use App\Enums\ThemeType;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\Theme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArtistSongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get JSON of Artist Index page content
        $artist_wiki_contents = file_get_contents(WikiPages::ARTIST_INDEX);
        $artist_wiki_json = json_decode($artist_wiki_contents);
        $artist_wiki_content_md = $artist_wiki_json->data->content_md;

        // Match Artist Entries
        // Format: "[{Artist Name}](/r/AnimeThemes/wiki/artist/{Artist Slug}/)"
        preg_match_all('/\[(.*)\]\(\/r\/AnimeThemes\/wiki\/artist\/(.*)\)/m', $artist_wiki_content_md, $artist_wiki_entries, PREG_SET_ORDER);

        foreach ($artist_wiki_entries as $artist_wiki_entry) {
            $artist_name = html_entity_decode($artist_wiki_entry[1]);
            $artist_slug = html_entity_decode($artist_wiki_entry[2]);

            $artist = Artist::where('name', $artist_name)->where('slug', $artist_slug)->first();
            if ($artist === null) {
                continue;
            }

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Artist Entry page content
            $artist_link = WikiPages::getArtistPage($artist_slug);
            $artist_song_wiki_contents = file_get_contents($artist_link);
            $artist_song_wiki_json = json_decode($artist_song_wiki_contents);
            $artist_song_wiki_content_md = $artist_song_wiki_json->data->content_md;

            // We want to proceed line by line
            preg_match_all('/^(.*)$/m', $artist_song_wiki_content_md, $artist_song_wiki_entries, PREG_SET_ORDER);

            // The current Anime
            $anime = null;

            foreach ($artist_song_wiki_entries as $artist_song_wiki_entry) {
                $wiki_entry_line = html_entity_decode($artist_song_wiki_entry[0]);

                // If Anime heading line, attempt to set current
                // Format: "###[{Anime Name}]({Resource Link})"
                if (preg_match('/^###\[(.*)\]\(https\:\/\/.*\)(?:\\r)?$/', $wiki_entry_line, $anime_name)) {
                    try {
                        // Set current Anime if we have a definitive match
                        // This is not guaranteed as an Anime Name may be inconsistent between indices
                        $matching_anime = Anime::where('name', html_entity_decode($anime_name[1]));
                        if ($matching_anime->count() === 1) {
                            $anime = $matching_anime->first();
                            continue;
                        }
                    } catch (\Exception $exception) {
                        Log::error($exception);
                    }

                    $anime = null;
                    continue;
                }

                // If Theme line, attempt to load Theme and associate Song to Artist
                // Format: "{OP|ED}{Sequence} V{Version} "{Song Title}" by {by}|[Webm {Tags}](https://animethemes.moe/video/{Video Basename})|{Episodes}|{Notes}"
                if (! is_null($anime) && preg_match('/^(OP|ED)(\d*)(?:\sV(\d*))?.*\"(.*)\".*\|\[Webm.*\]\(https\:\/\/animethemes\.moe\/video\/(.*)\)\|(.*)\|(.*)(?:\\r)?$/', $wiki_entry_line, $theme_match)) {
                    $theme_type = ThemeType::getValue(Str::upper($theme_match[1]));
                    $sequence = is_numeric($theme_match[2]) ? intval($theme_match[2]) : null;
                    $version = is_numeric($theme_match[3]) ? intval($theme_match[3]) : null;

                    if ($version === null || intval($version) === 1) {
                        $matching_themes = Theme::where('anime_id', $anime->anime_id)
                            ->where('type', $theme_type)
                            ->where(function ($query) use ($sequence) {
                                if (intval($sequence) === 1) {
                                    // Edge Case: "OP|ED" has become "OP1|ED1"
                                    $query->where('sequence', $sequence)->orWhereNull('sequence');
                                } else {
                                    $query->where('sequence', $sequence);
                                }
                            })
                            ->get();

                        if ($matching_themes->count() === 1) {
                            $theme = $matching_themes->first();
                            $song = $theme->song;

                            if ($song !== null && ! $artist->songs->contains($song)) {
                                Log::info("Attaching song '{$song->title}' to artist '{$artist->name}'");
                                $artist->songs()->attach($song);
                            }
                        }
                    }
                }
            }
        }
    }
}
