<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class ResourceSite extends Enum implements LocalizedEnum
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

    /**
     * Get domain by resource site.
     * TODO: Domain should be an attribute of the type.
     *
     * @param int $value the resource site key
     * @return string|null
     */
    public static function getDomain($value)
    {
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

        return null;
    }

    /**
     * Get resource site by link, matching expected domain.
     * TODO: Domain should be an attribute of the type.
     *
     * @param string $link the link to test
     * @return string|null
     */
    public static function valueOf($link)
    {
        $parsed_host = parse_url($link, PHP_URL_HOST);

        foreach (ResourceSite::getValues() as $value) {
            if ($parsed_host === ResourceSite::getDomain($value)) {
                return $value;
            }
        }

        return null;
    }
}
