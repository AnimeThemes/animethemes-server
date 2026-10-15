<?php

declare(strict_types=1);

use App\Enums\Models\Admin\ActivityStatus;
use App\Enums\Models\List\ExternalEntryStatus;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\AnimeFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Enums\Pivots\Document\PageRoleType;

return [
    ActivityStatus::class => [
        ActivityStatus::RUNNING->name => 'Running',
        ActivityStatus::FAILED->name => 'Failed',
        ActivityStatus::FINISHED->name => 'Finished',
    ],
    AnimeFormat::class => [
        AnimeFormat::TV->name => 'TV',
        AnimeFormat::TV_SHORT->name => 'TV Short',
        AnimeFormat::OVA->name => 'OVA',
        AnimeFormat::MOVIE->name => 'Movie',
        AnimeFormat::SPECIAL->name => 'Special',
        AnimeFormat::ONA->name => 'ONA',
    ],
    AnimeSeason::class => [
        AnimeSeason::WINTER->name => 'Winter',
        AnimeSeason::SPRING->name => 'Spring',
        AnimeSeason::SUMMER->name => 'Summer',
        AnimeSeason::FALL->name => 'Fall',
    ],
    ExternalEntryStatus::class => [
        ExternalEntryStatus::WATCHING->name => 'Watching',
        ExternalEntryStatus::COMPLETED->name => 'Completed',
        ExternalEntryStatus::PAUSED->name => 'Paused',
        ExternalEntryStatus::DROPPED->name => 'Dropped',
        ExternalEntryStatus::PLAN_TO_WATCH->name => 'Plan to Watch',
        ExternalEntryStatus::REWATCHING->name => 'Rewatching',
    ],
    ExternalProfileSite::class => [
        ExternalProfileSite::MAL->name => 'MyAnimeList',
        ExternalProfileSite::ANILIST->name => 'AniList',
    ],
    ExternalProfileVisibility::class => [
        ExternalProfileVisibility::PUBLIC->name => 'Public',
        ExternalProfileVisibility::PRIVATE->name => 'Private',
    ],
    ImageFacet::class => [
        ImageFacet::SMALL_COVER->name => 'Small Cover',
        ImageFacet::LARGE_COVER->name => 'Large Cover',
        ImageFacet::GRILL->name => 'Grill',
        ImageFacet::DOCUMENT->name => 'Document',
        ImageFacet::AVATAR->name => 'Avatar',
        ImageFacet::BANNER->name => 'Banner',
    ],
    PageRoleType::class => [
        PageRoleType::VIEWER->name => 'Viewer',
        PageRoleType::EDITOR->name => 'Editor',
    ],
    PlaylistVisibility::class => [
        PlaylistVisibility::PUBLIC->name => 'Public',
        PlaylistVisibility::PRIVATE->name => 'Private',
        PlaylistVisibility::UNLISTED->name => 'Unlisted',
    ],
    ResourceSite::class => [
        ResourceSite::AMAZON_MUSIC->name => 'Amazon Music',
        ResourceSite::AMAZON_PRIME_VIDEO->name => 'Amazon Prime Video',
        ResourceSite::ANIDB->name => 'aniDB',
        ResourceSite::ANILIST->name => 'AniList',
        ResourceSite::ANIME_PLANET->name => 'Anime-Planet',
        ResourceSite::ANN->name => 'Anime News Network',
        ResourceSite::APPLE_MUSIC->name => 'Apple Music',
        ResourceSite::CRUNCHYROLL->name => 'Crunchyroll',
        ResourceSite::DISNEY_PLUS->name => 'Disney Plus',
        ResourceSite::HIDIVE->name => 'HIDIVE',
        ResourceSite::HULU->name => 'Hulu',
        ResourceSite::INSTAGRAM->name => 'Instagram',
        ResourceSite::KITSU->name => 'Kitsu',
        ResourceSite::LIVECHART->name => 'LiveChart',
        ResourceSite::MAL->name => 'MyAnimeList',
        ResourceSite::NETFLIX->name => 'Netflix',
        ResourceSite::OFFICIAL_SITE->name => 'Official Website',
        ResourceSite::SPOTIFY->name => 'Spotify',
        ResourceSite::TIKTOK->name => 'TikTok',
        ResourceSite::X->name => 'X',
        ResourceSite::YOUTUBE->name => 'YouTube',
        ResourceSite::YOUTUBE_MUSIC->name => 'YouTube Music',
        ResourceSite::WIKI->name => 'Wiki',
    ],
    ThemeType::class => [
        ThemeType::OP->name => 'OP',
        ThemeType::ED->name => 'ED',
        ThemeType::IN->name => 'IN',
    ],
    VideoOverlap::class => [
        VideoOverlap::NONE->name => 'None',
        VideoOverlap::TRANS->name => 'Transition',
        VideoOverlap::OVER->name => 'Over',
    ],
    VideoSource::class => [
        VideoSource::WEB->name => 'WEB',
        VideoSource::RAW->name => 'RAW',
        VideoSource::BD->name => 'BD',
        VideoSource::DVD->name => 'DVD',
        VideoSource::VHS->name => 'VHS',
        VideoSource::LD->name => 'LD',
    ],
];
