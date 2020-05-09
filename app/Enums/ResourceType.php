<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ResourceType extends Enum
{

    // Official Media
    const OFFICIAL_SITE = 0;
    const TWITTER = 1;

    // Tracking Sites
    const ANIDB = 2;
    const ANILIST = 3;
    const ANIME_PLANET = 4;
    const ANN = 5;
    const KITSU = 6;
    const MAL = 7;

    // Compendia
    const WIKI = 8;

    public static function getDomain($value) {
        switch ($value) {
            case self::TWITTER:
                return 'twitter.com';
            case self::ANIDB:
                return 'anidb.net';
            case self::ANILIST:
                return 'anilist.co';
            case self::ANIME_PLANET:
                return 'anime-planet.com';
            case self::ANN:
                return 'animenewsnetwork.com';
            case self::KITSU:
                return 'kitsu.io';
            case self::MAL:
                return 'myanimelist.net';
            case self::WIKI:
                return 'wikipedia.org';
        }

        return NULL;
    }
}
