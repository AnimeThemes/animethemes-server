<?php

namespace App\DataManager;

use App\Models\Anime;
use App\Models\Theme;
use App\Models\Serie;
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
            if ($c=preg_match_all ('/##(\d+) (.*?) Season/m', $line, $matches)) {
                $currentSeason = Utils::getSeasonfromString($matches[2][0]);
            }

            // Anime Line
            if ($c=preg_match_all ('/###\[(.*)\]\((.*)\)/m', $line, $matches)) {
                $currentTheme = null;
                $currentAnime = DatabaseManager::addAnime($matches[1][0], $currentCollection, $currentSeason, $matches[2][0]);
            // Anime Line Alternative - No Link - some misc entries
            } else if ($c=preg_match_all ('/###(.*)/m', $line, $matches)) {
                $currentTheme = null;
                $currentAnime = DatabaseManager::addAnime($matches[1][0], $currentCollection, $currentSeason, null);
            }

            // Theme Line
            if ($c=preg_match_all ('/([A-Z][A-Z])?(\d+)? V?(\d+)?.*?\"(.*?)\".*?\|\[(.*?)\]\((.*?)\)\|(.*?)?\|(.*)?/m', $line, $matches)) {
                $currentTheme = DatabaseManager::addTheme($currentAnime->id, $matches[4][0], $matches[1][0], $matches[2][0], $matches[3][0], $matches[7][0], $matches[8][0]);
                DatabaseManager::addVideo($currentTheme->id, $matches[5][0], $matches[6][0]);
            // Theme Line Failsafe - misc entries
            } else if ($c=preg_match_all ('/([A-Z][A-Z])?(\d+)? V?(\d+)?.*?\"(.*?)\".*?\|\[(.*?)\]\((.*?)\)\|/m', $line, $matches)) {
                $currentTheme = DatabaseManager::addTheme($currentAnime->id, $matches[4][0], $matches[1][0], $matches[2][0], $matches[3][0], null, null);
                DatabaseManager::addVideo($currentTheme->id, $matches[5][0], $matches[6][0]);
            }

            // Mirrors
            if ($c=preg_match_all ('/\|\|\[(.*?)\]\((.*?)\)\|(.*?)?\|(.*)?/m', $line, $matches)) {
                DatabaseManager::addVideo($currentTheme->id, $matches[1][0], $matches[2][0]);
            }
        }
    }

    public static function getSerie($serieName, $serie) {

        // Download Markdown
        $serieUrl = "https://www.reddit.com/r/animethemes/wiki/series/$serie.json";
        $collectionLines = preg_split('/$\R?^/m', json_decode(file_get_contents($serieUrl))->data->content_md);
        
        $currentSerie = Serie::where('name', $serieName)->first();

        if ($currentSerie === null) {
            $currentSerie = Serie::create(array(
                'name' => $serieName
            ));
        }

        // Loop Markdown Lines
        for ($i = 0; $i < count($collectionLines); $i++) {
            $line = $collectionLines[$i]; // A Line

            // Anime Line
            if ($c=preg_match_all ('/###\[(.*)\]\(https:\/\/myanimelist\.net\/anime\/(\d+)\)/m', $line, $matches)) {
                $dbAnime = Anime::where('mal_id', $matches[2][0])->first();

                if ($dbAnime !== null) {
                    $dbAnime->serie_id = $currentSerie->id;
                    $dbAnime->save();
                }
            }
        }
    }

    public static function RegisterCollections() {
        $collections = Utils::getCollectionsIds();

        for ($i = 0; $i < count($collections); $i++) {
            $currentCollection = $collections[$i];
            Log::info("get-collection: $currentCollection");
            self::getCollection($currentCollection);
        }
    }

    public static function RegisterSeries() {
        $series = Utils::getSeriesIds();

        foreach($series as $key=>$value) {
            Log::info("get-serie: $key");
            self::getSerie($key, $value);
        }
    }
}
