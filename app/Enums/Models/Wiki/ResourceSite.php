<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Enums\BaseEnum;
use Illuminate\Support\Arr;

/**
 * Class ResourceSite.
 *
 * @method static static OFFICIAL_SITE()
 * @method static static TWITTER()
 * @method static static ANIDB()
 * @method static static ANILIST()
 * @method static static ANIME_PLANET()
 * @method static static ANN()
 * @method static static KITSU()
 * @method static static MAL()
 * @method static static WIKI()
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
     * @param  int|null  $value  the resource site key
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
     * @param  string  $link  the link to test
     * @return ResourceSite|null
     */
    public static function valueOf(string $link): ?ResourceSite
    {
        $parsedHost = parse_url($link, PHP_URL_HOST);

        return Arr::first(
            ResourceSite::getInstances(),
            fn (ResourceSite $site) => $parsedHost === ResourceSite::getDomain($site->value)
        );
    }
}
