<?php

declare(strict_types=1);

use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;

return [
    AnimeSeason::class => [
        AnimeSeason::WINTER->name => 'Winter',
        AnimeSeason::SPRING->name => 'Spring',
        AnimeSeason::SUMMER->name => 'Summer',
        AnimeSeason::FALL->name => 'Fall',
    ],
    BalanceFrequency::class => [
        BalanceFrequency::ONCE->name => 'Once',
        BalanceFrequency::ANNUALLY->name => 'Annually',
        BalanceFrequency::BIANNUALLY->name => 'Biannually',
        BalanceFrequency::QUARTERLY->name => 'Quarterly',
        BalanceFrequency::MONTHLY->name => 'Monthly',
    ],
    ImageFacet::class => [
        ImageFacet::COVER_SMALL->name => 'Small Cover',
        ImageFacet::COVER_LARGE->name => 'Large Cover',
    ],
    PlaylistVisibility::class => [
        PlaylistVisibility::PUBLIC->name => 'Public',
        PlaylistVisibility::PRIVATE->name => 'Private',
        PlaylistVisibility::UNLISTED->name => 'Unlisted',
    ],
    ResourceSite::class => [
        ResourceSite::OFFICIAL_SITE->name => 'Official Website',
        ResourceSite::TWITTER->name => 'Twitter',
        ResourceSite::ANIDB->name => 'aniDB',
        ResourceSite::ANILIST->name => 'AniList',
        ResourceSite::ANIME_PLANET->name => 'Anime-Planet',
        ResourceSite::ANN->name => 'Anime News Network',
        ResourceSite::KITSU->name => 'Kitsu',
        ResourceSite::MAL->name => 'MyAnimeList',
        ResourceSite::WIKI->name => 'Wiki',
    ],
    Service::class => [
        Service::OTHER->name => 'Other',
        Service::DIGITALOCEAN->name => 'DigitalOcean',
        Service::AWS->name => 'AWS',
        Service::HOVER->name => 'Hover',
        Service::WALKERSERVERS->name => 'WalkerServers',
    ],
    ThemeType::class => [
        ThemeType::OP->name => 'OP',
        ThemeType::ED->name => 'ED',
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
