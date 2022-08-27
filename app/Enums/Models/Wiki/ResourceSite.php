<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Enums\BaseEnum;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            self::ANIME_PLANET => 'www.anime-planet.com',
            self::ANN => 'www.animenewsnetwork.com',
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

    /**
     * Attempt to parse External ID from Site Link.
     *
     * @param  string  $link
     * @return string|null
     */
    public static function parseIdFromLink(string $link): ?string
    {
        $site = ResourceSite::valueOf($link);

        return match ($site?->value) {
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANN,
            ResourceSite::MAL => Str::match('/\d+/', $link),
            ResourceSite::ANIME_PLANET => self::parseAnimePlanetIdFromLink($link),
            ResourceSite::KITSU => self::parseKitsuIdFromLink($link),
            default => null,
        };
    }

    /**
     * Attempt to parse Anime Planet ID from link.
     *
     * @param  string  $link
     * @return string|null
     */
    protected static function parseAnimePlanetIdFromLink(string $link): ?string
    {
        // We only want to attempt to parse the ID for an anime resource
        if (Str::match('/^https:\/\/www\.anime-planet\.com\/anime\/[a-zA-Z0-9-]+$/', $link) !== $link) {
            return null;
        }

        try {
            $response = Http::get($link)
                ->throw()
                ->body();

            return Str::match(
                '/["\']?ENTRY_INFO["\']? *: *{.*id["\']? *: *["\']?(\d+)["\']? *,/s',
                $response
            );
        } catch (RequestException $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    /**
     * Attempt to parse Kitsu ID from link.
     *
     * @param  string  $link
     * @return string|null
     */
    protected static function parseKitsuIdFromLink(string $link): ?string
    {
        try {
            $query = '
            query ($slug: String!) {
                findAnimeBySlug(slug: $slug) {
                    id
                }
            }
            ';

            $variables = [
                'slug' => Str::afterLast($link, '/'),
            ];

            $response = Http::post('https://kitsu.io/api/graphql', [
                'query' => $query,
                'variables' => $variables,
            ])
                ->throw()
                ->json();

            return Arr::get($response, 'data.findAnimeBySlug.id');
        } catch (RequestException $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    /**
     * Get the URL of the site for anime resources.
     *
     * @param  ResourceSite  $site
     * @param  int  $id
     * @param  string|null  $slug
     * @return string|null
     */
    public static function formatAnimeResourceLink(ResourceSite $site, int $id, ?string $slug = null): ?string
    {
        return match ($site->value) {
            ResourceSite::ANIDB => "https://anidb.net/anime/$id/",
            ResourceSite::ANILIST => "https://anilist.co/anime/$id/",
            ResourceSite::ANIME_PLANET => "https://www.anime-planet.com/anime/$slug",
            ResourceSite::ANN => "https://www.animenewsnetwork.com/encyclopedia/anime.php?id=$id",
            ResourceSite::KITSU => "https://kitsu.io/anime/$slug",
            ResourceSite::MAL => "https://myanimelist.net/anime/$id/",
            default => null,
        };
    }

    /**
     * Get the URL of the site for studio resources.
     *
     * @param  ResourceSite  $site
     * @param  int  $id
     * @param  string|null  $slug
     * @return string|null
     */
    public static function formatStudioResourceLink(ResourceSite $site, int $id, ?string $slug = null): ?string
    {
        return match ($site->value) {
            ResourceSite::ANIDB => "https://anidb.net/creator/$id/",
            ResourceSite::ANILIST => "https://anilist.co/studio/$id/",
            ResourceSite::ANIME_PLANET => "https://www.anime-planet.com/anime/studios/$slug",
            ResourceSite::ANN => "https://www.animenewsnetwork.com/encyclopedia/company.php?id=$id",
            ResourceSite::MAL => "https://myanimelist.net/anime/producer/$id/",
            default => null,
        };
    }
}
