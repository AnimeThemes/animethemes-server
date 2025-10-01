<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use App\Contracts\Models\HasResources;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Rules\Wiki\Resource\AnimeResourceLinkFormatRule;
use App\Rules\Wiki\Resource\AnimeThemeEntryResourceLinkFormatRule;
use App\Rules\Wiki\Resource\ArtistResourceLinkFormatRule;
use App\Rules\Wiki\Resource\SongResourceLinkFormatRule;
use App\Rules\Wiki\Resource\StudioResourceLinkFormatRule;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use RuntimeException;

enum ResourceSite: int implements HasLabel
{
    use LocalizesName;

    // Official Media
    case OFFICIAL_SITE = 0;
    case X = 1;

    // Tracking Sites
    case ANIDB = 2;
    case ANILIST = 3;
    case ANIME_PLANET = 4;
    case ANN = 5;
    case KITSU = 6;
    case MAL = 7;
    case LIVECHART = 20;

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

    public static function getDomain(?int $value): ?string
    {
        return match ($value) {
            ResourceSite::X->value => 'x.com',
            ResourceSite::ANIDB->value => 'anidb.net',
            ResourceSite::ANILIST->value => 'anilist.co',
            ResourceSite::ANIME_PLANET->value => 'www.anime-planet.com',
            ResourceSite::ANN->value => 'www.animenewsnetwork.com',
            ResourceSite::KITSU->value => 'kitsu.app',
            ResourceSite::LIVECHART->value => 'www.livechart.me',
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
     */
    public static function valueOf(string $link): ?ResourceSite
    {
        $parsedHost = Uri::of($link)->host();

        return Arr::first(
            ResourceSite::cases(),
            fn (ResourceSite $site): bool => $parsedHost === ResourceSite::getDomain($site->value)
        );
    }

    public static function parseIdFromLink(string $link): ?string
    {
        $site = ResourceSite::valueOf($link);

        return match ($site) {
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANN,
            ResourceSite::MAL,
            ResourceSite::NETFLIX,
            ResourceSite::LIVECHART,
            ResourceSite::APPLE_MUSIC => Str::match('/\d+/', $link),
            ResourceSite::ANIME_PLANET => ResourceSite::parseAnimePlanetIdFromLink($link),
            ResourceSite::KITSU => ResourceSite::parseKitsuIdFromLink($link),
            default => null,
        };
    }

    protected static function parseAnimePlanetIdFromLink(string $link): ?string
    {
        // We only want to attempt to parse the ID for an anime resource
        if (Str::match('/^https?:\/\/www\.anime-planet\.com\/anime\/[a-zA-Z0-9-]+$/', $link) !== $link) {
            return null;
        }

        try {
            $response = Http::withUserAgent('AnimeThemes/1.0 (https://animethemes.moe)')
                ->get($link)
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

    protected static function parseKitsuIdFromLink(string $link): ?string
    {
        try {
            if ($id = Str::match('/^https?:\/\/kitsu\.app\/anime\/(\d+)/', $link)) {
                return $id;
            }

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
     * @throws RuntimeException
     */
    public function getFormatRule(Model&HasResources $model): ValidationRule
    {
        return match (true) {
            $model instanceof Anime => new AnimeResourceLinkFormatRule($this),
            $model instanceof AnimeThemeEntry => new AnimeThemeEntryResourceLinkFormatRule($this),
            $model instanceof Artist => new ArtistResourceLinkFormatRule($this),
            $model instanceof Song => new SongResourceLinkFormatRule($this),
            $model instanceof Studio => new StudioResourceLinkFormatRule($this),
            default => throw new RuntimeException('The model does not have a resource link format rule.'),
        };
    }

    /**
     * Get the resource sites available for determined model.
     *
     * @param  class-string|null  $modelClass
     * @return ResourceSite[]
     */
    public static function getForModel(?string $modelClass): array
    {
        return match ($modelClass) {
            Anime::class => [
                ResourceSite::X,
                ResourceSite::ANIDB,
                ResourceSite::ANILIST,
                ResourceSite::ANIME_PLANET,
                ResourceSite::ANN,
                ResourceSite::KITSU,
                ResourceSite::MAL,
                ResourceSite::YOUTUBE,
                ResourceSite::CRUNCHYROLL,
                ResourceSite::HIDIVE,
                ResourceSite::NETFLIX,
                ResourceSite::DISNEY_PLUS,
                ResourceSite::HULU,
                ResourceSite::AMAZON_PRIME_VIDEO,
                ResourceSite::OFFICIAL_SITE,
                ResourceSite::WIKI,
                ResourceSite::LIVECHART,
            ],
            AnimeThemeEntry::class => [
                ResourceSite::YOUTUBE,
            ],
            Artist::class => [
                ResourceSite::X,
                ResourceSite::ANIDB,
                ResourceSite::ANILIST,
                ResourceSite::ANIME_PLANET,
                ResourceSite::ANN,
                ResourceSite::MAL,
                ResourceSite::SPOTIFY,
                ResourceSite::YOUTUBE_MUSIC,
                ResourceSite::YOUTUBE,
                ResourceSite::OFFICIAL_SITE,
                ResourceSite::WIKI,
            ],
            Song::class => [
                ResourceSite::ANIDB,
                ResourceSite::SPOTIFY,
                ResourceSite::YOUTUBE_MUSIC,
                ResourceSite::YOUTUBE,
                ResourceSite::APPLE_MUSIC,
                ResourceSite::AMAZON_MUSIC,
                ResourceSite::OFFICIAL_SITE,
                ResourceSite::WIKI,
            ],
            Studio::class => [
                ResourceSite::X,
                ResourceSite::ANIDB,
                ResourceSite::ANILIST,
                ResourceSite::ANIME_PLANET,
                ResourceSite::ANN,
                ResourceSite::MAL,
                ResourceSite::OFFICIAL_SITE,
                ResourceSite::WIKI,
            ],
            default => ResourceSite::cases(),
        };
    }

    /**
     * Get the URL of the site for resources by determined model.
     *
     * @param  class-string  $modelClass
     */
    public function formatResourceLink(string $modelClass, ?int $id = null, ?string $slug = null, ?string $type = null): ?string
    {
        if ($modelClass === Anime::class) {
            return match ($this) {
                ResourceSite::X => "https://x.com/$slug",
                ResourceSite::ANIDB => "https://anidb.net/anime/$id",
                ResourceSite::ANILIST => "https://anilist.co/anime/$id",
                ResourceSite::ANIME_PLANET => "https://www.anime-planet.com/anime/$slug",
                ResourceSite::ANN => "https://www.animenewsnetwork.com/encyclopedia/anime.php?id=$id",
                ResourceSite::KITSU => "https://kitsu.app/anime/$id",
                ResourceSite::LIVECHART => "https://www.livechart.me/anime/$id",
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

        if ($modelClass === AnimeThemeEntry::class) {
            return match ($this) {
                ResourceSite::YOUTUBE => "https://www.youtube.com/watch?v=$slug",
                default => null,
            };
        }

        if ($modelClass === Artist::class) {
            return match ($this) {
                ResourceSite::X => "https://x.com/$slug",
                ResourceSite::ANIDB => "https://anidb.net/creator/$id",
                ResourceSite::ANILIST => "https://anilist.co/staff/$id",
                ResourceSite::ANIME_PLANET => "https://www.anime-planet.com/people/$slug",
                ResourceSite::ANN => "https://www.animenewsnetwork.com/encyclopedia/people.php?id=$id",
                ResourceSite::MAL => "https://myanimelist.net/people/$id",
                ResourceSite::SPOTIFY => "https://open.spotify.com/artist/$slug",
                ResourceSite::YOUTUBE_MUSIC => "https://music.youtube.com/channel/$slug",
                ResourceSite::YOUTUBE => "https://www.youtube.com/@$slug",
                default => null,
            };
        }

        if ($modelClass === Song::class) {
            return match ($this) {
                ResourceSite::ANIDB => "https://anidb.net/song/$id",
                ResourceSite::SPOTIFY => "https://open.spotify.com/track/$slug",
                ResourceSite::YOUTUBE_MUSIC => "https://music.youtube.com/watch?v=$slug",
                ResourceSite::YOUTUBE => "https://www.youtube.com/watch?v=$slug",
                ResourceSite::APPLE_MUSIC => "https://music.apple.com/jp/album/$id",
                ResourceSite::AMAZON_MUSIC => "https://music.amazon.co.jp/tracks/$slug",
                default => null,
            };
        }

        if ($modelClass === Studio::class) {
            return match ($this) {
                ResourceSite::X => "https://x.com/$slug",
                ResourceSite::ANIDB => "https://anidb.net/creator/$id",
                ResourceSite::ANILIST => "https://anilist.co/studio/$id",
                ResourceSite::ANIME_PLANET => "https://www.anime-planet.com/anime/studios/$slug",
                ResourceSite::ANN => "https://www.animenewsnetwork.com/encyclopedia/company.php?id=$id",
                ResourceSite::MAL => "https://myanimelist.net/anime/producer/$id",
                default => null,
            };
        }

        return null;
    }

    public function usesIdInLink(): bool
    {
        return match ($this) {
            ResourceSite::ANIME_PLANET => false,
            default => true,
        };
    }

    public function getUrlCaptureGroups(?Model $model): string
    {
        // The first capture group refers to $type, the second to $id and $slug of the formatResourceLink method.
        if ($model instanceof Anime) {
            return match ($this) {
                ResourceSite::X => '/^https:\/\/(x)\.com\/(\w+)/',
                ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/(anime)\/(\d+)$/',
                ResourceSite::ANIME_PLANET => '/^https:\/\/www\.anime-planet\.com\/(anime)\/([a-zA-Z0-9-]+)$/',
                ResourceSite::ANN => '/^https:\/\/www\.animenewsnetwork\.com\/encyclopedia\/(anime)\.php\?id=(\d+)$/',
                ResourceSite::KITSU => '/^https:\/\/kitsu\.app\/(anime)\/([a-zA-Z0-9-]+)$/',
                ResourceSite::LIVECHART => '/^https:\/\/www\.livechart\.me\/(anime)\/(\d+)$/',
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

        if ($model instanceof AnimeThemeEntry) {
            return match ($this) {
                ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/(watch)\?v=([\w-]+)$/',
                default => '/^$/',
            };
        }

        if ($model instanceof Artist) {
            return match ($this) {
                ResourceSite::X => '/^https:\/\/(x)\.com\/(\w+)$/',
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
                ResourceSite::SPOTIFY => '/^https:\/\/open\.spotify\.com\/(track)\/(\w+)$/',
                ResourceSite::YOUTUBE_MUSIC => '/^https:\/\/music\.youtube\.com\/(watch)\?v=([\w-]+)$/',
                ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/(watch)\?v=([\w-]+)$/',
                ResourceSite::APPLE_MUSIC => '/^https:\/\/music\.apple\.com\/jp\/(album)\/(\d+)$/',
                ResourceSite::AMAZON_MUSIC => '/^https:\/\/music\.amazon\.co\.jp\/(tracks)\/(\w+)$/',
                default => '/^$/',
            };
        }

        if ($model instanceof Studio) {
            return match ($this) {
                ResourceSite::X => '/^https:\/\/(x)\.com\/(\w+)$/',
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

    /**
     * @param  class-string|null  $modelClass
     */
    public function getPattern(?string $modelClass): ?string
    {
        if ($modelClass === Anime::class) {
            return match ($this) {
                ResourceSite::X => '/^https:\/\/x\.com\/\w+$/',
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/anime\/\d+$/',
                ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/anime\/\d+$/',
                ResourceSite::ANIME_PLANET => '/^https:\/\/www\.anime-planet\.com\/anime\/[a-zA-Z0-9-]+$/',
                ResourceSite::ANN => '/^https:\/\/www\.animenewsnetwork\.com\/encyclopedia\/anime\.php\?id=\d+$/',
                ResourceSite::KITSU => '/^https:\/\/kitsu\.app\/anime\/[a-zA-Z0-9-]+$/',
                ResourceSite::LIVECHART => '/^https:\/\/www\.livechart\.me\/anime\/\d+$/',
                ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/anime\/\d+$/',
                ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/\@[\w-]+$/',
                ResourceSite::CRUNCHYROLL => '/^https:\/\/www\.crunchyroll\.com\/(?:series|watch|null)\/\w+$/',
                ResourceSite::HIDIVE => '/^https:\/\/www\.hidive\.com\/(?:tv|movies|null)\/[\w-]+$/',
                ResourceSite::NETFLIX => '/^https:\/\/www\.netflix\.com\/(?:title|watch|null)\/\d+$/',
                ResourceSite::DISNEY_PLUS => '/^https:\/\/www\.disneyplus\.com\/(?:series|movies|null)\/[\w-]+\/\w+$/',
                ResourceSite::HULU => '/^https:\/\/www\.hulu\.com\/(?:series|watch|movie|null)\/[\w-]+$/',
                ResourceSite::AMAZON_PRIME_VIDEO => '/^https:\/\/www\.primevideo\.com\/detail\/\w+$/',
                ResourceSite::OFFICIAL_SITE,
                ResourceSite::WIKI => null,
                default => '/$.^/',
            };
        }

        if ($modelClass === AnimeThemeEntry::class) {
            return match ($this) {
                ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/watch\?v=[\w-]+$/',
                default => '/$.^/',
            };
        }

        if ($modelClass === Artist::class) {
            return match ($this) {
                ResourceSite::X => '/^https:\/\/x\.com\/\w+$/',
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/creator\/(?:virtual\/)?\d+$/',
                ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/staff\/\d+$/',
                ResourceSite::ANIME_PLANET => '/^https:\/\/www\.anime-planet\.com\/people\/[a-zA-Z0-9-]+$/',
                ResourceSite::ANN => '/^https:\/\/www\.animenewsnetwork\.com\/encyclopedia\/people\.php\?id=\d+$/',
                ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/people\/\d+$/',
                ResourceSite::SPOTIFY => '/^https:\/\/open\.spotify\.com\/artist\/\w+$/',
                ResourceSite::YOUTUBE_MUSIC => '/^https:\/\/music\.youtube\.com\/channel\/[\w-]+/',
                ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/\@[\w-]+$/',
                ResourceSite::OFFICIAL_SITE,
                ResourceSite::WIKI => null,
                default => '/$.^/',
            };
        }

        if ($modelClass === Song::class) {
            return match ($this) {
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/song\/\d+$/',
                ResourceSite::SPOTIFY => '/^https:\/\/open\.spotify\.com\/track\/\w+$/',
                ResourceSite::YOUTUBE_MUSIC => '/^https:\/\/music\.youtube\.com\/watch\?v=[\w-]+$/',
                ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/watch\?v=[\w-]+$/',
                ResourceSite::APPLE_MUSIC => '/^https:\/\/music\.apple\.com\/jp\/album\/\d+$/',
                ResourceSite::AMAZON_MUSIC => '/^https:\/\/music\.amazon\.co\.jp\/tracks\/\w+$/',
                ResourceSite::OFFICIAL_SITE,
                ResourceSite::WIKI => null,
                default => '/$.^/',
            };
        }

        if ($modelClass === Studio::class) {
            return match ($this) {
                ResourceSite::X => '/^https:\/\/x\.com\/\w+$/',
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/creator\/(?:virtual\/)?\d+$/',
                ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/studio\/\d+$/',
                ResourceSite::ANIME_PLANET => '/^https:\/\/www\.anime-planet\.com\/anime\/studios\/[a-zA-Z0-9-]+$/',
                ResourceSite::ANN => '/^https:\/\/www\.animenewsnetwork\.com\/encyclopedia\/company\.php\?id=\d+$/',
                ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/anime\/producer\/\d+$/',
                ResourceSite::OFFICIAL_SITE,
                ResourceSite::WIKI => null,
                default => '/$.^/',
            };
        }

        return null;
    }
}
