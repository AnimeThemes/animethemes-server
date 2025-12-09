<?php

declare(strict_types=1);

use App\Enums\Models\Admin\ActionLogStatus;
use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\User\SubmissionStatus;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;

return [
    ActionLogStatus::class => [
        ActionLogStatus::RUNNING->name => 'Running',
        ActionLogStatus::FAILED->name => 'Failed',
        ActionLogStatus::FINISHED->name => 'Finished',
    ],
    AnimeMediaFormat::class => [
        AnimeMediaFormat::UNKNOWN->name => 'Unknown',
        AnimeMediaFormat::TV->name => 'TV',
        AnimeMediaFormat::TV_SHORT->name => 'TV Short',
        AnimeMediaFormat::OVA->name => 'OVA',
        AnimeMediaFormat::MOVIE->name => 'Movie',
        AnimeMediaFormat::SPECIAL->name => 'Special',
        AnimeMediaFormat::ONA->name => 'ONA',
    ],
    AnimeSeason::class => [
        AnimeSeason::WINTER->name => 'Winter',
        AnimeSeason::SPRING->name => 'Spring',
        AnimeSeason::SUMMER->name => 'Summer',
        AnimeSeason::FALL->name => 'Fall',
    ],
    AnimeSynonymType::class => [
        AnimeSynonymType::OTHER->name => 'Other',
        AnimeSynonymType::NATIVE->name => 'Native',
        AnimeSynonymType::ENGLISH->name => 'English',
        AnimeSynonymType::SHORT->name => 'Short',
    ],
    ExternalEntryWatchStatus::class => [
        ExternalEntryWatchStatus::WATCHING->name => 'Watching',
        ExternalEntryWatchStatus::COMPLETED->name => 'Completed',
        ExternalEntryWatchStatus::PAUSED->name => 'Paused',
        ExternalEntryWatchStatus::DROPPED->name => 'Dropped',
        ExternalEntryWatchStatus::PLAN_TO_WATCH->name => 'Plan to Watch',
        ExternalEntryWatchStatus::REWATCHING->name => 'Rewatching',
    ],
    ExternalProfileSite::class => [
        ExternalProfileSite::MAL->name => 'MyAnimeList',
        ExternalProfileSite::ANILIST->name => 'AniList',
        ExternalProfileSite::KITSU->name => 'Kitsu',
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
    PlaylistVisibility::class => [
        PlaylistVisibility::PUBLIC->name => 'Public',
        PlaylistVisibility::PRIVATE->name => 'Private',
        PlaylistVisibility::UNLISTED->name => 'Unlisted',
    ],
    SubmissionStatus::class => [
        SubmissionStatus::PENDING->name => 'Pending',
        SubmissionStatus::CHANGES_REQUESTED->name => 'Changes Requested',
        SubmissionStatus::REJECTED->name => 'Rejected',
        SubmissionStatus::PARTIALLY_APPROVED->name => 'Partially Approved',
        SubmissionStatus::APPROVED->name => 'Approved',
    ],
    ResourceSite::class => [
        ResourceSite::OFFICIAL_SITE->name => 'Official Website',
        ResourceSite::X->name => 'X',
        ResourceSite::ANIDB->name => 'aniDB',
        ResourceSite::ANILIST->name => 'AniList',
        ResourceSite::ANIME_PLANET->name => 'Anime-Planet',
        ResourceSite::ANN->name => 'Anime News Network',
        ResourceSite::KITSU->name => 'Kitsu',
        ResourceSite::LIVECHART->name => 'LiveChart',
        ResourceSite::MAL->name => 'MyAnimeList',
        ResourceSite::WIKI->name => 'Wiki',
        ResourceSite::SPOTIFY->name => 'Spotify',
        ResourceSite::YOUTUBE_MUSIC->name => 'YouTube Music',
        ResourceSite::YOUTUBE->name => 'YouTube',
        ResourceSite::APPLE_MUSIC->name => 'Apple Music',
        ResourceSite::AMAZON_MUSIC->name => 'Amazon Music',
        ResourceSite::CRUNCHYROLL->name => 'Crunchyroll',
        ResourceSite::HIDIVE->name => 'HIDIVE',
        ResourceSite::NETFLIX->name => 'Netflix',
        ResourceSite::DISNEY_PLUS->name => 'Disney Plus',
        ResourceSite::HULU->name => 'Hulu',
        ResourceSite::AMAZON_PRIME_VIDEO->name => 'Amazon Prime Video',
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
