<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ThemeType;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\Theme;
use App\Pivots\ArtistSong;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class ArtistSongSeeder
 * @package Database\Seeders
 */
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
        $artistWikiContents = WikiPages::getPageContents(WikiPages::ARTIST_INDEX);
        if ($artistWikiContents === null) {
            return;
        }

        // Match Artist Entries
        // Format: "[{Artist Name}](/r/AnimeThemes/wiki/artist/{Artist Slug}/)"
        preg_match_all('/\[(.*)\]\(\/r\/AnimeThemes\/wiki\/artist\/(.*)\)/m', $artistWikiContents, $artistWikiEntries, PREG_SET_ORDER);

        foreach ($artistWikiEntries as $artistWikiEntry) {
            $artistName = html_entity_decode($artistWikiEntry[1]);
            $artistSlug = html_entity_decode($artistWikiEntry[2]);

            $artist = Artist::where('name', $artistName)->where('slug', $artistSlug)->first();
            if ($artist === null) {
                continue;
            }

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Artist Entry page content
            $artistLink = WikiPages::getArtistPage($artistSlug);
            $artistSongWikiContents = WikiPages::getPageContents($artistLink);
            if ($artistSongWikiContents === null) {
                continue;
            }

            // We want to proceed line by line
            preg_match_all('/^(.*)$/m', $artistSongWikiContents, $artistSongWikiEntries, PREG_SET_ORDER);

            // The current Anime
            $anime = null;

            foreach ($artistSongWikiEntries as $artistSongWikiEntry) {
                $wikiEntryLine = html_entity_decode($artistSongWikiEntry[0]);

                // If Anime heading line, attempt to set current
                // Format: "###[{Anime Name}]({Resource Link})"
                if (preg_match('/^###\[(.*)\]\(https\:\/\/.*\)(?:\\r)?$/', $wikiEntryLine, $animeName)) {
                    try {
                        // Set current Anime if we have a definitive match
                        // This is not guaranteed as an Anime Name may be inconsistent between indices
                        $matchingAnime = Anime::where('name', html_entity_decode($animeName[1]));
                        if ($matchingAnime->count() === 1) {
                            $anime = $matchingAnime->first();
                            continue;
                        }
                    } catch (Exception $e) {
                        Log::error($e->getMessage());
                    }

                    $anime = null;
                    continue;
                }

                // If Theme line, attempt to load Theme and associate Song to Artist
                // Format: "{OP|ED}{Sequence} V{Version} "{Song Title}" by {by}|[Webm {Tags}](https://animethemes.moe/video/{Video Basename})|{Episodes}|{Notes}"
                if ($anime !== null && preg_match('/^(OP|ED)(\d*)(?:\sV(\d*))?.*\"(.*)\".*\|\[Webm.*\]\(https\:\/\/animethemes\.moe\/video\/(.*)\)\|(.*)\|(.*)(?:\\r)?$/', $wikiEntryLine, $themeMatch)) {
                    $themeType = ThemeType::getValue(Str::upper($themeMatch[1]));
                    $sequence = is_numeric($themeMatch[2]) ? intval($themeMatch[2]) : null;
                    $version = is_numeric($themeMatch[3]) ? intval($themeMatch[3]) : null;

                    if ($version === null || $version === 1) {
                        $matchingThemes = Theme::where('anime_id', $anime->anime_id)
                            ->where('type', $themeType)
                            ->where(function (Builder $query) use ($sequence) {
                                if (intval($sequence) === 1) {
                                    // Edge Case: "OP|ED" has become "OP1|ED1"
                                    $query->where('sequence', $sequence)->orWhereNull('sequence');
                                } else {
                                    $query->where('sequence', $sequence);
                                }
                            })
                            ->get();

                        if ($matchingThemes->count() === 1) {
                            $theme = $matchingThemes->first();
                            $song = $theme->song;

                            if ($song !== null && ArtistSong::where($artist->getKeyName(), $artist->getKey())->where($song->getKeyName(), $song->getKey())->doesntExist()) {
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
