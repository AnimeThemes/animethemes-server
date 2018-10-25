<?php

namespace App\DataManager;

use App\Models\Anime;
use App\Models\AnimeName;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DatabaseManager
{
    public static function addAnime($animeName, $animeCollection, $animeSeason, $link) {
        $dbAnime = Anime::where('name', $animeName)
        ->where('collection', $animeCollection)
        ->where('season', $animeSeason)->first();

        if ($dbAnime === null) { // Check if already in database
            $newAnime = array();
            $newAnime["name"] = $animeName;
            $newAnime["collection"] = $animeCollection;
            $newAnime["season"] = $animeSeason;
            if ($link !== null) {
                if ($c=preg_match_all ('/https:\/\/myanimelist\.net\/anime\/(\d+)/m', $link, $mal)) {
                    $newAnime["mal_id"] = $mal[1][0];
                } else if ($c=preg_match_all ('/https:\/\/anidb\.net\/perl-bin\/animedb\.pl\?show=anime&aid=(\d+)/m', $link, $anidb)) {
                    $newAnime["anidb_id"] = $anidb[1][0];
                }
            }
            //Log::info('add-anime', $newAnime);
            $dbAnime = Anime::create($newAnime);
        }

        return $dbAnime;
    }

    public static function addTheme($animeId, $song, $theme, $major, $minor, $episodes, $notes) {
        $newTheme = array();

        $dbTheme = Theme::where('anime_id', $animeId)
        ->where('theme', $theme)
        ->where('song_name', $song)
        ->where('ver_major', $major)
        ->where('ver_minor', $minor)->first();

        if ($dbTheme === null) {
            $newTheme["anime_id"] = $animeId;

            if ($notes !== null) {
                // Set if NSFW
                if ($c=preg_match_all ('/(NSFW)/m', $notes, $n1)) {
                    $newTheme["isNSFW"] = true;
                } else {
                    $newTheme["isNSFW"] = false;
                }

                // Set if Spoiler
                if ($c=preg_match_all ('/(Spoiler)/m', $notes, $n1)) {
                    $newTheme["isSpoiler"] = true;
                } else {
                    $newTheme["isSpoiler"] = false;
                }
            } else {
                $newTheme["isNSFW"] = false;
                $newTheme["isSpoiler"] = false;
            }

            $newTheme["song_name"] = $song;
            $newTheme["theme"] = $theme;

            // Set Versions
            if ($major === '') {
                $newTheme["ver_major"] = '1';
            } else {
                $newTheme["ver_major"] = $major;
            }

            if ($minor === '') {
                $newTheme["ver_minor"] = '1';
            } else {
                $newTheme["ver_minor"] = $minor;
            }

            $newTheme["episodes"] = $episodes;
            $newTheme["notes"] = $notes;

            $dbTheme = Theme::create($newTheme);
        } else {
            if ($dbTheme->episodes !== $episodes || $dbTheme->notes !== $notes) {
                $dbTheme->episodes = $episodes;
                $dbTheme->notes = $notes;
                $dbTheme->save();
            }
        }

        return $dbTheme;
    }

    public static function addVideo($theme_id, $videoTitle, $videoLink) {
        $newVideo = array();
        // Set quality
        if ($c=preg_match_all ('/(\d+)/m', $videoTitle, $link)) {
            $newVideo["quality"] = $link[1][0];
        } else {
            $newVideo["quality"] = '720';
        }

        // Set if NC
        if ($c=preg_match_all ('/(NC)/m', $videoTitle, $link)) {
            $newVideo["isNC"] = true;
        } else {
            $newVideo["isNC"] = false;
        }

        // Set if Lyrics
        if ($c=preg_match_all ('/(Lyrics)/m', $videoTitle, $link)) {
            $newVideo["isLyrics"] = true;
        } else {
            $newVideo["isLyrics"] = false;
        }

        // Set source
        if ($c=preg_match_all ('/(BD|DVD|VHS)/m', $videoTitle, $link)) {
            $newVideo["source"] = $link[1][0];
        } else {
            $newVideo["source"] = "TV/UNK";
        }

        // Check if exist and mod table
        if ($c=preg_match_all ('/https:\/\/animethemes\.moe\/video\/(.*).webm/m', $videoLink, $link)) {
            $video = Video::where('filename', $link[1][0])->first();
            if ($video !== null) {
                if ($video->theme_id !== $theme_id) {
                    $video->theme_id = $theme_id;
                    $video->quality = $newVideo["quality"];
                    $video->isNC = $newVideo["isNC"];
                    $video->isLyrics = $newVideo["isLyrics"];
                    $video->source = $newVideo["source"];
                    //Log::info('edit-video', $newVideo);
                    $video->save();
                }
            } else {
                Log::error("no video with name {$link[1][0]}");
            }
        } else if ($videoLink !== ""){
            $newVideo["url"] = $videoLink;
            Log::notice('video-upload', $newVideo);
        }
    }
}
