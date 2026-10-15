<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use App\Contracts\Models\HasResources;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Rules\Wiki\Resource\ModelResourceLinkFormatRule;
use Filament\Support\Contracts\HasLabel;
use GraphQL\Type\Definition\Deprecated;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use RuntimeException;

enum ResourceSite: int implements HasLabel
{
    use LocalizesName;

    // Official Media
    case OFFICIAL_SITE = 0;
    case X = 1;
    case INSTAGRAM = 21;
    case TIKTOK = 22;

    // Tracking Sites
    case ANIDB = 2;
    case ANILIST = 3;
    #[Deprecated('No longer actively maintained')]
    case ANIME_PLANET = 4;
    #[Deprecated('No longer actively maintained')]
    case ANN = 5;
    #[Deprecated('No longer actively maintained')]
    case KITSU = 6;
    case MAL = 7;
    case LIVECHART = 20;

    // Compendia
    #[Deprecated('No longer actively maintained')]
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
            ResourceSite::AMAZON_MUSIC->value => 'music.amazon.co.jp',
            ResourceSite::AMAZON_PRIME_VIDEO->value => 'www.primevideo.com',
            ResourceSite::ANIDB->value => 'anidb.net',
            ResourceSite::ANILIST->value => 'anilist.co',
            ResourceSite::APPLE_MUSIC->value => 'music.apple.com',
            ResourceSite::CRUNCHYROLL->value => 'www.crunchyroll.com',
            ResourceSite::DISNEY_PLUS->value => 'www.disneyplus.com',
            ResourceSite::HIDIVE->value => 'www.hidive.com',
            ResourceSite::HULU->value => 'www.hulu.com',
            ResourceSite::INSTAGRAM->value => 'www.instagram.com',
            ResourceSite::LIVECHART->value => 'www.livechart.me',
            ResourceSite::MAL->value => 'myanimelist.net',
            ResourceSite::NETFLIX->value => 'www.netflix.com',
            ResourceSite::SPOTIFY->value => 'open.spotify.com',
            ResourceSite::TIKTOK->value => 'www.tiktok.com',
            ResourceSite::YOUTUBE_MUSIC->value => 'music.youtube.com',
            ResourceSite::YOUTUBE->value => 'www.youtube.com',
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
            ResourceSite::MAL,
            ResourceSite::NETFLIX,
            ResourceSite::LIVECHART,
            ResourceSite::APPLE_MUSIC => Str::match('/\d+/', $link),
            default => null,
        };
    }

    /**
     * @throws RuntimeException
     */
    public function getFormatRule(Model&HasResources $model): ValidationRule
    {
        return match (true) {
            $model instanceof Anime => new ModelResourceLinkFormatRule($this, Anime::class),
            $model instanceof Entry => new ModelResourceLinkFormatRule($this, Entry::class),
            $model instanceof Artist => new ModelResourceLinkFormatRule($this, Artist::class),
            $model instanceof Song => new ModelResourceLinkFormatRule($this, Song::class),
            $model instanceof Studio => new ModelResourceLinkFormatRule($this, Studio::class),
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
                ResourceSite::MAL,
                ResourceSite::YOUTUBE,
                ResourceSite::CRUNCHYROLL,
                ResourceSite::HIDIVE,
                ResourceSite::NETFLIX,
                ResourceSite::DISNEY_PLUS,
                ResourceSite::HULU,
                ResourceSite::AMAZON_PRIME_VIDEO,
                ResourceSite::OFFICIAL_SITE,
                ResourceSite::LIVECHART,
            ],
            Entry::class => [
                ResourceSite::YOUTUBE,
            ],
            Artist::class => [
                ResourceSite::X,
                ResourceSite::ANIDB,
                ResourceSite::ANILIST,
                ResourceSite::INSTAGRAM,
                ResourceSite::MAL,
                ResourceSite::OFFICIAL_SITE,
                ResourceSite::SPOTIFY,
                ResourceSite::TIKTOK,
                ResourceSite::YOUTUBE_MUSIC,
                ResourceSite::YOUTUBE,
            ],
            Song::class => [
                ResourceSite::ANIDB,
                ResourceSite::SPOTIFY,
                ResourceSite::YOUTUBE_MUSIC,
                ResourceSite::YOUTUBE,
                ResourceSite::APPLE_MUSIC,
                ResourceSite::AMAZON_MUSIC,
                ResourceSite::OFFICIAL_SITE,
            ],
            Studio::class => [
                ResourceSite::X,
                ResourceSite::ANIDB,
                ResourceSite::ANILIST,
                ResourceSite::MAL,
                ResourceSite::OFFICIAL_SITE,
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
                ResourceSite::AMAZON_PRIME_VIDEO => "https://www.primevideo.com/detail/$slug",
                ResourceSite::ANIDB => "https://anidb.net/anime/$id",
                ResourceSite::ANILIST => "https://anilist.co/anime/$id",
                ResourceSite::CRUNCHYROLL => "https://www.crunchyroll.com/$type/$slug",
                ResourceSite::DISNEY_PLUS => "https://www.disneyplus.com/$type/$slug/$id",
                ResourceSite::HIDIVE => "https://www.hidive.com/$type/$slug",
                ResourceSite::HULU => "https://www.hulu.com/$type/$slug",
                ResourceSite::LIVECHART => "https://www.livechart.me/anime/$id",
                ResourceSite::MAL => "https://myanimelist.net/anime/$id",
                ResourceSite::NETFLIX => "https://www.netflix.com/$type/$id",
                ResourceSite::X => "https://x.com/$slug",
                ResourceSite::YOUTUBE => "https://www.youtube.com/@$slug",
                default => null,
            };
        }

        if ($modelClass === Entry::class) {
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
                ResourceSite::INSTAGRAM => "https://www.instagram.com/$slug",
                ResourceSite::MAL => "https://myanimelist.net/people/$id",
                ResourceSite::SPOTIFY => "https://open.spotify.com/artist/$slug",
                ResourceSite::TIKTOK => "https://www.tiktok.com/@$slug",
                ResourceSite::YOUTUBE => "https://www.youtube.com/@$slug",
                ResourceSite::YOUTUBE_MUSIC => "https://music.youtube.com/channel/$slug",
                default => null,
            };
        }

        if ($modelClass === Song::class) {
            return match ($this) {
                ResourceSite::AMAZON_MUSIC => "https://music.amazon.co.jp/tracks/$slug",
                ResourceSite::ANIDB => "https://anidb.net/song/$id",
                ResourceSite::APPLE_MUSIC => "https://music.apple.com/jp/album/$id",
                ResourceSite::SPOTIFY => "https://open.spotify.com/track/$slug",
                ResourceSite::YOUTUBE => "https://www.youtube.com/watch?v=$slug",
                ResourceSite::YOUTUBE_MUSIC => "https://music.youtube.com/watch?v=$slug",
                default => null,
            };
        }

        if ($modelClass === Studio::class) {
            return match ($this) {
                ResourceSite::ANIDB => "https://anidb.net/creator/$id",
                ResourceSite::ANILIST => "https://anilist.co/studio/$id",
                ResourceSite::MAL => "https://myanimelist.net/anime/producer/$id",
                ResourceSite::X => "https://x.com/$slug",
                default => null,
            };
        }

        return null;
    }

    /**
     * @param  class-string|null  $modelClass
     */
    public function getPattern(?string $modelClass): ?string
    {
        if ($modelClass === Anime::class) {
            return match ($this) {
                ResourceSite::AMAZON_PRIME_VIDEO => '/^https:\/\/www\.primevideo\.com\/detail\/\w+$/',
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/anime\/\d+$/',
                ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/anime\/\d+$/',
                ResourceSite::CRUNCHYROLL => '/^https:\/\/www\.crunchyroll\.com\/(?:series|watch|null)\/\w+$/',
                ResourceSite::DISNEY_PLUS => '/^https:\/\/www\.disneyplus\.com\/(?:series|movies|null)\/[\w-]+\/\w+$/',
                ResourceSite::HIDIVE => '/^https:\/\/www\.hidive\.com\/(?:tv|movies|null)\/[\w-]+$/',
                ResourceSite::HULU => '/^https:\/\/www\.hulu\.com\/(?:series|watch|movie|null)\/[\w-]+$/',
                ResourceSite::LIVECHART => '/^https:\/\/www\.livechart\.me\/anime\/\d+$/',
                ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/anime\/\d+$/',
                ResourceSite::NETFLIX => '/^https:\/\/www\.netflix\.com\/(?:title|watch|null)\/\d+$/',
                ResourceSite::OFFICIAL_SITE => null,
                ResourceSite::X => '/^https:\/\/x\.com\/\w+$/',
                ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/\@[\w-]+$/',
                default => '/$.^/',
            };
        }

        if ($modelClass === Entry::class) {
            return match ($this) {
                ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/watch\?v=[\w-]+$/',
                default => '/$.^/',
            };
        }

        if ($modelClass === Artist::class) {
            return match ($this) {
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/creator\/(?:virtual\/)?\d+$/',
                ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/staff\/\d+$/',
                ResourceSite::INSTAGRAM => '/^https:\/\/www\.instagram\.com\/[\w.-]+$/',
                ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/people\/\d+$/',
                ResourceSite::OFFICIAL_SITE => null,
                ResourceSite::SPOTIFY => '/^https:\/\/open\.spotify\.com\/artist\/\w+$/',
                ResourceSite::TIKTOK => '/^https:\/\/www\.tiktok\.com\/@[\w.-]+$/',
                ResourceSite::X => '/^https:\/\/x\.com\/\w+$/',
                ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/(?:@[\w.-]+|channel\/[\w.-]+)$/',
                ResourceSite::YOUTUBE_MUSIC => '/^https:\/\/music\.youtube\.com\/channel\/[\w.-]+/',
                default => '/$.^/',
            };
        }

        if ($modelClass === Song::class) {
            return match ($this) {
                ResourceSite::AMAZON_MUSIC => '/^https:\/\/music\.amazon\.co\.jp\/tracks\/\w+$/',
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/song\/\d+$/',
                ResourceSite::APPLE_MUSIC => '/^https:\/\/music\.apple\.com\/jp\/album\/\d+$/',
                ResourceSite::SPOTIFY => '/^https:\/\/open\.spotify\.com\/track\/\w+$/',
                ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/watch\?v=[\w-]+$/',
                ResourceSite::YOUTUBE_MUSIC => '/^https:\/\/music\.youtube\.com\/watch\?v=[\w-]+$/',
                default => '/$.^/',
            };
        }

        if ($modelClass === Studio::class) {
            return match ($this) {
                ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/creator\/(?:virtual\/)?\d+$/',
                ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/studio\/\d+$/',
                ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/anime\/producer\/\d+$/',
                ResourceSite::OFFICIAL_SITE => null,
                ResourceSite::X => '/^https:\/\/x\.com\/\w+$/',
                default => '/$.^/',
            };
        }

        return null;
    }
}
