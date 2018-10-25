<?php

namespace App\DataManager;

use App\Models\Anime;
use App\Models\AnimeName;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Support\Facades\Log;

class Utils
{
    public static function getCollectionsIds() {
        $link_yearindex = 'https://www.reddit.com/r/animethemes/wiki/year_index.json';
        $lines = preg_split('/$\R?^/m', json_decode(file_get_contents($link_yearindex))->data->content_md); // Get
        $collections = array();
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];
            if ($c=preg_match_all ('/###\[.*?\]\(https:\/\/www\.reddit\.com\/r\/AnimeThemes\/wiki\/(.*?)\)/m', $line, $matches)) {
                $collections[] = $matches[1][0];
            }
        }
        $collections[] = "misc";
        return $collections;
    }

    /**
     * Define season for anime based on yearseason like 20180 for Winter 2018
     * 
     * 0 - Winter
     * 1 - Spring
     * 2 - Summer
     * 3 - Fall
     * 4 - All (Default)
     */
    public static function getSeasonfromString($text) {
        if ($text == "Winter") {
            return 0;
        } else if ($text == "Spring") {
            return 1;
        } else if ($text == "Summer") {
            return 2;
        } else if ($text == "Fall" || $text == "Autumn") {
            return 3;
        } else {
            return 4;
        }
    }

    public static function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}