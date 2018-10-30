<?php

namespace App\DataManager;

use App\Models\Anime;
use App\Models\Theme;
use App\Models\Serie;
use App\Models\Artist;
use App\Models\Video;
use Illuminate\Support\Facades\Log;

class RedditParser
{
    public static function getCollection($collection) {
        // Init Variables
        $currentAnime = null;
        $currentTheme = null;

        $currentCollection = $collection;
        $currentSeason = 4;

        // Download Markdown
        $collectionUrl = "https://www.reddit.com/r/animethemes/wiki/$collection.json";
        $collectionLines = preg_split('/$\R?^/m', json_decode(file_get_contents($collectionUrl))->data->content_md);
        
        // Loop Markdown Lines
        for ($i = 0; $i < count($collectionLines); $i++) {
            $line = $collectionLines[$i]; // A Line

            // Season Line
            if (preg_match('/##(\d+) (.*?) Season/u', $line, $seasonRegex)) {
                $currentSeason = Utils::getSeasonfromString($seasonRegex[2]);
            }

            // Anime Line
            if (preg_match('/###\[(.*)\]\((.*)\)/u', $line, $animeRegex)) {
                $currentTheme = null;
                $currentAnime = DatabaseManager::addAnime($animeRegex[1], $currentCollection, $currentSeason, $animeRegex[2]);
            // Anime Line Alternative - No Link - some misc entries
            } else if (preg_match('/###(.*)/u', $line, $animeRegex)) {
                $currentTheme = null;
                $currentAnime = DatabaseManager::addAnime($animeRegex[1], $currentCollection, $currentSeason, null);
            }

            // Theme Line
            if (preg_match('/([A-Z][A-Z])?(\d+)? V?(\d+)?.*?\"(.*?)\".*?\|\[(.*?)\]\((.*?)\)\|(.*?)?\|([^\s]*)?/u', $line, $themeRegex)) {
                $currentTheme = DatabaseManager::addTheme($currentAnime->id, 
                $themeRegex[4], 
                $themeRegex[1], 
                $themeRegex[2], 
                $themeRegex[3], 
                $themeRegex[7], 
                $themeRegex[8]);
                DatabaseManager::addVideo($currentTheme->id, 
                $themeRegex[5], 
                $themeRegex[6]);
            // Theme Line Failsafe - misc entries
            } else if (preg_match('/([A-Z][A-Z])?(\d+)? V?(\d+)?.*?\"(.*?)\".*?\|\[(.*?)\]\((.*?)\)\|/u', $line, $themeRegex)) {
                $currentTheme = DatabaseManager::addTheme($currentAnime->id, 
                $themeRegex[4], 
                $themeRegex[1], 
                $themeRegex[2], 
                $themeRegex[3], 
                null, 
                null);
                DatabaseManager::addVideo($currentTheme->id, 
                $themeRegex[5], 
                $themeRegex[6]);
            }

            // Mirrors
            if (preg_match('/\|\|\[(.*?)\]\((.*?)\)\|(.*?)?\|(.*)?/u', $line, $videoRegex)) {
                DatabaseManager::addVideo($currentTheme->id, 
                $videoRegex[1], // Video Title eg. Webm (BD, NC, 1080)
                $videoRegex[2]); // Video Url
            }
        }
    }

    public static function getSerie($serieName, $serie) {

        // Download Markdown
        $serieUrl = "https://www.reddit.com/r/animethemes/wiki/series/$serie.json";
        $collectionLines = preg_split('/$\R?^/m', json_decode(file_get_contents($serieUrl))->data->content_md);

        $currentSerie = Serie::where('name', $serieName)->first() ?? Serie::create(array('name' => $serieName));

        // Loop Markdown Lines
        for ($i = 0; $i < count($collectionLines); $i++) {
            $line = $collectionLines[$i]; // A Line

            // Anime Line
            if (preg_match('/###\[(.*)\]\(https:\/\/myanimelist\.net\/anime\/(\d+).*?\)/u', $line, $animeRegex)) {
                $dbAnime = Anime::where('mal_id', $animeRegex[2])->first();
                if ($dbAnime !== null) {
                    $dbAnime->serie_id = $currentSerie->id;
                    $dbAnime->save();
                }
            }
        }
    }

    public static function getArtist($artistName, $artist) {

        // Download Markdown
        $artistUrl = "https://www.reddit.com/r/animethemes/wiki/artist/$artist.json";
        $collectionLines = preg_split('/$\R?^/m', json_decode(file_get_contents($artistUrl))->data->content_md);

        $currentArtist = Artist::where('name', $artistName)->first() ?? Artist::create(array('name' => $artistName));
        
        $currentAnime = null;

        // Loop Markdown Lines
        for ($i = 0; $i < count($collectionLines); $i++) {
            $line = $collectionLines[$i]; // A Line

            // Anime Line
            if (preg_match('/###\[(.*)\]\(https:\/\/myanimelist\.net\/anime\/(\d+).*?\)/u', $line, $animeRegex)) {
                $currentAnime = Anime::where('mal_id', $animeRegex[2])->first();
            }

            if ($currentAnime !== null) {
                // Theme Line
                if (preg_match('/([A-Z][A-Z])?(\d+)? V?(\d+)?.*?\"(.*?)\".*?/u', $line, $themeRegex)) {
                    // Set Versions
                    $major = empty($themeRegex[2]) ? '1' : $themeRegex[2];
                    $minor = empty($themeRegex[3]) ? '1' : $themeRegex[3];

                    $currentTheme = Theme::where('anime_id', $currentAnime->id)
                        ->where('theme', $themeRegex[1])
                        ->where('song_name', $themeRegex[4])
                        ->where('ver_major', $major)
                        ->where('ver_minor', $minor)->first();
                    if ($currentTheme !== null) {
                        $currentTheme->artist_id = $currentArtist->id;
                        $currentTheme->save();
                    }
                }
            }
        }
    }

    public static function RegisterCollections() {
        $collections = Utils::getCollectionsIds();

        for ($i = 0; $i < count($collections); $i++) {
            $currentCollection = $collections[$i];
            Log::info("get-collection", array('collection' => $currentCollection));
            self::getCollection($currentCollection);
        }
    }

    public static function RegisterSeries() {
        $series = Utils::getSeriesIds();

        foreach($series as $key=>$value) {
            Log::info("get-serie", array('serieName' => $key, 'serieId' => $value));
            self::getSerie($key, $value);
        }
    }

    public static function RegisterArtists() {
        $artists = Utils::getArtistsIds();

        foreach($artists as $key=>$value) {
            Log::info("get-artist", array('artistName' => $key, 'artistId' => $value));
            self::getArtist($key, $value);
        }
    }
}
