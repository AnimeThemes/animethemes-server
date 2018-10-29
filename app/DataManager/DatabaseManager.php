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
            ->where('season', $animeSeason)->first() 
        ?? Anime::create(array(
            'name' => $animeName,
            'slug' => Utils::slugify("$animeCollection$animeSeason-$animeName"),
            'collection' => $animeCollection,
            'season' => $animeSeason,
            'mal_id' => ($link !== null) ? (preg_match('/https:\/\/myanimelist\.net\/anime\/(\d+)/u', $link, $malRegex) ? $malRegex[1] : null) : null,
            'anidb_id' => ($link !== null) ? (preg_match('/https:\/\/anidb\.net\/perl-bin\/animedb\.pl\?show=anime&aid=(\d+)/u', $link, $anidbRegex) ? $anidbRegex[1] : null) : null
        ));
        Log::info('anime', $dbAnime->toArray());
        return $dbAnime;
    }

    public static function addTheme($animeId, $song, $theme, $major, $minor, $episodes, $notes) {
        // Check if empty
        $major = empty($major) ? '1' : $major;
        $minor = empty($minor) ? '1' : $minor;

        $dbTheme = Theme::where('anime_id', $animeId)
            ->where('theme', $theme)
            ->where('song_name', $song)
            ->where('ver_major', $major)
            ->where('ver_minor', $minor)->first() 
        ?? Theme::create(array(
            'anime_id' => $animeId,
            'song_name' => $song,
            'slug' => Utils::slugify("$animeId-$theme.$major.V$minor-$song"),
            'theme' => $theme,
            'ver_major' => $major,
            'ver_minor' => $minor,
            'episodes' => $episodes,
            'notes' => $notes,
            'isSpoiler' => ($notes !== null) ? preg_match('/(Spoiler)/u', $notes, $n1) : false,
            'isNSFW' => ($notes !== null) ? preg_match('/(NSFW)/u', $notes, $n2) : false,
        ));
        Log::info('theme', $dbTheme->toArray());
        // Check for mismatches
        if ($dbTheme->episodes !== $episodes || $dbTheme->notes !== $notes) { 
            $dbTheme->episodes = $episodes;
            $dbTheme->notes = $notes;
            Log::info('theme-edited');
            $dbTheme->save();
        }

        return $dbTheme;
    }

    public static function addVideo($theme_id, $videoTitle, $videoLink) {
        // Check if exist and mod table
        if (preg_match('/https:\/\/animethemes\.moe\/video\/(.*).webm/u', $videoLink, $link)) {
            $video = Video::where('filename', $link[1])->first();
            if ($video !== null) {
                if ($video->theme_id !== $theme_id) {
                    $video->theme_id = $theme_id;
                    $video->quality = preg_match('/(\d+)/u', $videoTitle, $qualityRegex) ? $qualityRegex[1] : '720';
                    $video->isNC = preg_match('/(NC)/u', $videoTitle, $n1);
                    $video->isLyrics = preg_match('/(Lyrics)/u', $videoTitle, $n2);
                    $video->isTrans = preg_match('/(Trans)/u', $videoTitle, $n3);
                    $video->isOver = preg_match('/(Over)/u', $videoTitle, $n4);
                    $video->isUncensored = preg_match('/(Uncen)/u', $videoTitle, $n5);
                    $video->isSubbed = preg_match('/(Subbed)/u', $videoTitle, $n6);
                    $video->source = preg_match('/(BD|DVD|VHS|VN|Game)/u', $videoTitle, $sourceRegex) ? $sourceRegex[1] : "Unknown";
                    $video->save();
                    Log::info('edit-video', $video->toArray());
                }
            } else {
                Log::error("no-video", array(
                    'theme_id' => $theme_id,
                    'videoTitle' => $videoTitle,
                    'videoLink' => $videoLink
                ));
            }
        } else if ($videoLink !== ""){
            Log::notice('video-upload', array(
                'theme_id' => $theme_id,
                'videoTitle' => $videoTitle,
                'videoLink' => $videoLink
            ));
        }
    }
}
