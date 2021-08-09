<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Enums\BaseEnum;

/**
 * Class ResourceSite.
 */
final class ResourceSite extends BaseEnum
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
     *
     * @param int|null $value the resource site key
     * @return string|null
     */
    public static function getDomain(?int $value): ?string
    {
        return match ($value) {
            self::TWITTER => 'twitter.com',
            self::ANIDB => 'anidb.net',
            self::ANILIST => 'anilist.co',
            self::ANIME_PLANET => 'anime-planet.com',
            self::ANN => 'animenewsnetwork.com',
            self::KITSU => 'kitsu.io',
            self::MAL => 'myanimelist.net',
            self::WIKI => 'wikipedia.org',
            default => null,
        };
    }

    /**
     * Get resource site by link, matching expected domain.
     *
     * @param string $link the link to test
     * @return int|null
     */
    public static function valueOf(string $link): ?int
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
