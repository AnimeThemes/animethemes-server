<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Model;
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

    // Song Resources
    case SPOTIFY = 9;
    case YOUTUBE_MUSIC = 10;
    case YOUTUBE = 11;
    case APPLE_MUSIC = 12;
    case AMAZON_MUSIC = 13;

    // Official Streaming
    case CRUNCHYROLL = 14;
    case HIDIVE = 15;
    case NETFLIX = 16;
    case DISNEY_PLUS = 17;
    case HULU = 18;
    case AMAZON_PRIME_VIDEO = 19;

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
            ResourceSite::SPOTIFY->value => 'open.spotify.com',
            ResourceSite::YOUTUBE_MUSIC->value => 'music.youtube.com',
            ResourceSite::YOUTUBE->value => 'www.youtube.com',
            ResourceSite::APPLE_MUSIC->value => 'music.apple.com',
            ResourceSite::AMAZON_MUSIC->value => 'music.amazon.co.jp',
            ResourceSite::CRUNCHYROLL->value => 'www.crunchyroll.com',
            ResourceSite::HIDIVE->value => 'www.hidive.com',
            ResourceSite::NETFLIX->value => 'www.netflix.com',
            ResourceSite::DISNEY_PLUS->value => 'www.disneyplus.com',
            ResourceSite::HULU->value => 'www.hulu.com',
            ResourceSite::AMAZON_PRIME_VIDEO->value => 'www.primevideo.com',
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
            ResourceSite::NETFLIX => Str::match('/\d+/', $link),
            ResourceSite::APPLE_MUSIC => Str::match('/\d+/', $link),
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
     * @param  string|null  $type
     * @return string|null
     */
    public function formatAnimeResourceLink(int $id, ?string $slug = null, ?string $type = null): ?string
    {
        return match ($this) {
            ResourceSite::TWITTER => "https://twitter.com/$slug",
            ResourceSite::ANIDB => "https://anidb.net/anime/$id",
            ResourceSite::ANILIST => "https://anilist.co/anime/$id",
            ResourceSite::ANIME_PLANET => "https://www.anime-planet.com/anime/$slug",
            ResourceSite::ANN => "https://www.animenewsnetwork.com/encyclopedia/anime.php?id=$id",
            ResourceSite::KITSU => "https://kitsu.io/anime/$slug",
            ResourceSite::MAL => "https://myanimelist.net/anime/$id",
            ResourceSite::YOUTUBE => "https://www.youtube.com/@$slug",
            ResourceSite::CRUNCHYROLL => "https://www.crunchyroll.com/$type/$slug",
            ResourceSite::HIDIVE => "https://www.hidive.com/$type/$slug",
            ResourceSite::NETFLIX => "https://www.netflix.com/$type/$id",
            ResourceSite::DISNEY_PLUS => "https://www.disneyplus.com/$type/$slug/$id",
            ResourceSite::HULU => "https://www.hulu.com/$type/$slug",
            ResourceSite::AMAZON_PRIME_VIDEO => "https://www.primevideo.com/detail/$slug",
            default => null,
        };
    }

    /**
     * Get the URL of the site for artist resources.
     *
     * @param  int  $id
     * @param  string|null  $slug
     * @param  string|null  $type
     * @return string|null
     */
    public function formatArtistResourceLink(int $id, ?string $slug = null, ?string $type = null): ?string
    {
        return match ($this) {
            ResourceSite::TWITTER => "https://twitter.com/$slug",
            ResourceSite::ANIDB => "https://anidb.net/creator/$id",
            ResourceSite::ANILIST => "https://anilist.co/staff/$id",
            ResourceSite::ANIME_PLANET => "https://www.anime-planet.com/people/$slug",
            ResourceSite::ANN => "https://www.animenewsnetwork.com/encyclopedia/people.php?id=$id",
            ResourceSite::MAL => "https://myanimelist.net/people/$id",
            ResourceSite::YOUTUBE => "https://www.youtube.com/@$slug",
            ResourceSite::SPOTIFY => "https://open.spotify.com/artist/$slug",
            default => null,
        };
    }

    /**
     * Get the URL of the site for song resources.
     *
     * @param  int  $id
     * @param  string|null  $slug
     * @param  string|null  $type
     * @return string|null
     */
    public function formatSongResourceLink(int $id, ?string $slug = null, ?string $type = null): ?string
    {
        return match ($this) {
            ResourceSite::SPOTIFY => "https://open.spotify.com/track/$slug",
            ResourceSite::YOUTUBE_MUSIC => "https://music.youtube.com/watch?v=$slug",
            ResourceSite::YOUTUBE => "https://www.youtube.com/watch?v=$slug",
            ResourceSite::APPLE_MUSIC => "https://music.apple.com/jp/album/$id",
            ResourceSite::AMAZON_MUSIC => "https://music.amazon.co.jp/tracks/$slug",
            default => null
        };
    }

    /**
     * Get the URL of the site for studio resources.
     *
     * @param  int  $id
     * @param  string|null  $slug
     * @param  string|null  $type
     * @return string|null
     */
    public function formatStudioResourceLink(int $id, ?string $slug = null, ?string $type = null): ?string
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

    /**
     * Get the URL capture groups of the resource site.
     *
     * @param  Model|null  $model
     * @return string
     */
    public function getUrlCaptureGroups(?Model $model): string
    {
        // The first capture group refers to $type, the second to $id and $slug of the formatting functions.
        if ($model instanceof Anime) {
            return match ($this) {
                ResourceSite::TWITTER => '/^https:\/\/(twitter)\.com\/(\w+)/',
                ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/(anime)\/(\d+)$/',
                ResourceSite::ANIME_PLANET => '/^https:\/\/www\.anime-planet\.com\/(anime)\/([a-zA-Z0-9-]+)$/',
                ResourceSite::ANN => '/^https:\/\/www\.animenewsnetwork\.com\/encyclopedia\/(anime)\.php\?id=(\d+)$/',
                ResourceSite::KITSU => '/^https:\/\/kitsu\.io\/(anime)\/([a-zA-Z0-9-]+)$/',
                ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/(anime)\/(\d+)$/',
                ResourceSite::YOUTUBE => '/^https:\/\/www\.(youtube)\.com\/\@([\w-]+)$/',
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/(anime)\/(\d+)$/',
                ResourceSite::CRUNCHYROLL => '/^https:\/\/www\.crunchyroll\.com\/(series|watch)\/(\w+)/',
                ResourceSite::HIDIVE => '/^https:\/\/www\.hidive\.com\/(tv|movies)\/([\w-]+)/',
                ResourceSite::NETFLIX => '/^https:\/\/www\.netflix\.com\/(title|watch)\/(\d+)/',
                ResourceSite::DISNEY_PLUS => '/^https:\/\/www\.disneyplus\.com\/(series|movies)\/([\w-]+\/\w+)/',
                ResourceSite::HULU => '/^https:\/\/www\.hulu\.com\/(series|watch|movie)\/([\w-]+)/',
                ResourceSite::AMAZON_PRIME_VIDEO => '/^https:\/\/www\.primevideo\.com\/(detail)\/(\w+)/',
                default => '/^$/',
            };
        }

        if ($model instanceof Artist) {
            return match ($this) {
                ResourceSite::TWITTER => '/^https:\/\/(twitter)\.com\/(\w+)$/',
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/(creator)\/(?:virtual\/)?(\d+)$/',
                ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/(staff)\/(\d+)$/',
                ResourceSite::ANIME_PLANET => '/^https:\/\/www\.anime-planet\.com\/(people)\/([a-zA-Z0-9-]+)$/',
                ResourceSite::ANN => '/^https:\/\/www\.animenewsnetwork\.com\/encyclopedia\/(people)\.php\?id=(\d+)$/',
                ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/(people)\/(\d+)$/',
                ResourceSite::SPOTIFY => '/^https:\/\/open\.spotify\.com\/(artist)\/([\w-]+)$/',
                ResourceSite::YOUTUBE_MUSIC => '/^https:\/\/music\.youtube\.com\/(channel)\/([\w-]+)$/',
                ResourceSite::YOUTUBE => '/^https:\/\/www\.(youtube)\.com\/\@([\w-]+)$/',
                default => '/^$/',
            };
        }

        if ($model instanceof Song) {
            return match ($this) {
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/(song)\/(\d+)$/',
                ResourceSite::SPOTIFY => '/^https:\/\/open\.spotify\.com\/track\/(\w+)$/',
                ResourceSite::YOUTUBE_MUSIC => '/^https:\/\/music\.youtube\.com\/(watch)\?v=([\w-]+)$/',
                ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/(watch)\?v=([\w-]+)$/',
                ResourceSite::APPLE_MUSIC => '/^https:\/\/music\.apple\.com\/jp\/(album)\/(\d+)$/',
                ResourceSite::AMAZON_MUSIC => '/^https:\/\/music\.amazon\.co\.jp\/(tracks)\/(\w+)$/',
                default => '/^$/',
            };
        }

        if ($model instanceof Studio) {
            return match ($this) {
                ResourceSite::TWITTER => '/^https:\/\/(twitter)\.com\/(\w+)$/',
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/(creator)\/(?:virtual\/)?(\d+)$/',
                ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/(studio)\/(\d+)$/',
                ResourceSite::ANIME_PLANET => '/^https:\/\/www\.anime-planet\.com\/anime\/(studios)\/([a-zA-Z0-9-]+)$/',
                ResourceSite::ANN => '/^https:\/\/www\.animenewsnetwork\.com\/encyclopedia\/(company)\.php\?id=(\d+)$/',
                ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/anime\/(producer)\/(\d+)$/',
                default => '/^$/',
            };
        }

        return '/^.*/';
    }
}
