<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Enum ResourceSite.
 */
enum ResourceSite: int
{
    use LocalizesName;

    // Official Media
    case OFFICIAL_SITE = 0;
    case TWITTER = 1;

    // Tracking Sites
    case ANIDB = 2;
    case ANILIST = 3;
    case ANIME_PLANET = 4;
    case ANN = 5;
    case KITSU = 6;
    case MAL = 7;

    // Compendia
    case WIKI = 8;

    /**
     * Get domain by resource site.
     *
     * @param  int|null  $value
     * @return string|null
     */
    public static function getDomain(?int $value): ?string
    {
        return match ($value) {
            ResourceSite::TWITTER->value => 'twitter.com',
            ResourceSite::ANIDB->value => 'anidb.net',
            ResourceSite::ANILIST->value => 'anilist.co',
            ResourceSite::ANIME_PLANET->value => 'www.anime-planet.com',
            ResourceSite::ANN->value => 'www.animenewsnetwork.com',
            ResourceSite::KITSU->value => 'kitsu.io',
            ResourceSite::MAL->value => 'myanimelist.net',
            ResourceSite::WIKI->value => 'wikipedia.org',
            default => null,
        };
    }

    /**
     * Get resource site by link, matching expected domain.
     *
     * @param  string  $link
     * @return ResourceSite|null
     */
    public static function valueOf(string $link): ?ResourceSite
    {
        $parsedHost = parse_url($link, PHP_URL_HOST);

        return Arr::first(
            ResourceSite::cases(),
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

        return match ($site) {
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANN,
            ResourceSite::MAL => Str::match('/\d+/', $link),
            ResourceSite::ANIME_PLANET => ResourceSite::parseAnimePlanetIdFromLink($link),
            ResourceSite::KITSU => ResourceSite::parseKitsuIdFromLink($link),
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
     * @param  int  $id
     * @param  string|null  $slug
     * @return string|null
     */
    public function formatAnimeResourceLink(int $id, ?string $slug = null): ?string
    {
        return match ($this) {
            ResourceSite::TWITTER => "https://twitter.com/$slug",
            ResourceSite::ANIDB => "https://anidb.net/anime/$id",
            ResourceSite::ANILIST => "https://anilist.co/anime/$id",
            ResourceSite::ANIME_PLANET => "https://www.anime-planet.com/anime/$slug",
            ResourceSite::ANN => "https://www.animenewsnetwork.com/encyclopedia/anime.php?id=$id",
            ResourceSite::KITSU => "https://kitsu.io/anime/$slug",
            ResourceSite::MAL => "https://myanimelist.net/anime/$id",
            default => null,
        };
    }

    /**
     * Get the URL of the site for artist resources.
     *
     * @param  int  $id
     * @param  string|null  $slug
     * @return string|null
     */
    public function formatArtistResourceLink(int $id, ?string $slug = null): ?string
    {
        return match ($this) {
            ResourceSite::TWITTER => "https://twitter.com/$slug",
            ResourceSite::ANIDB => "https://anidb.net/creator/$id",
            ResourceSite::ANILIST => "https://anilist.co/staff/$id",
            ResourceSite::ANIME_PLANET => "https://www.anime-planet.com/people/$slug",
            ResourceSite::ANN => "https://www.animenewsnetwork.com/encyclopedia/people.php?id=$id",
            ResourceSite::MAL => "https://myanimelist.net/people/$id",
            default => null,
        };
    }

    /**
     * Get the URL of the site for studio resources.
     *
     * @param  int  $id
     * @param  string|null  $slug
     * @return string|null
     */
    public function formatStudioResourceLink(int $id, ?string $slug = null): ?string
    {
        return match ($this) {
            ResourceSite::TWITTER => "https://twitter.com/$slug",
            ResourceSite::ANIDB => "https://anidb.net/creator/$id",
            ResourceSite::ANILIST => "https://anilist.co/studio/$id",
            ResourceSite::ANIME_PLANET => "https://www.anime-planet.com/anime/studios/$slug",
            ResourceSite::ANN => "https://www.animenewsnetwork.com/encyclopedia/company.php?id=$id",
            ResourceSite::MAL => "https://myanimelist.net/anime/producer/$id",
            default => null,
        };
    }
}
