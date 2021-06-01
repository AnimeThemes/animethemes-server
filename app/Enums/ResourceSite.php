<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Class ResourceSite
 * @package App\Enums
 */
final class ResourceSite extends Enum implements LocalizedEnum
{
    // Official Media
    public const OFFICIAL_SITE = 0;
    public const TWITTER = 1;

    // Tracking Sites
    public const ANIDB = 2;
    public const ANILIST = 3;
    public const ANIME_PLANET = 4;
    public const ANN = 5;
    public const KITSU = 6;
    public const MAL = 7;

    // Compendia
    public const WIKI = 8;

    /**
     * Get domain by resource site.
     * TODO: Domain should be an attribute of the type.
     *
     * @param int|null $value the resource site key
     * @return string|null
     */
    public static function getDomain(?int $value): ?string
    {
        if ($value === null) {
            return null;
        }

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
    public static function valueOf(string $link): ?string
    {
        $parsedHost = parse_url($link, PHP_URL_HOST);

        foreach (ResourceSite::getValues() as $value) {
            if ($parsedHost === ResourceSite::getDomain($value)) {
                return $value;
            }
        }

        return null;
    }
}
