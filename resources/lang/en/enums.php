<?php

declare(strict_types=1);

use App\Enums\Models\Auth\InvitationStatus;
use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;

return [
    AnimeSeason::class => [
        AnimeSeason::WINTER => 'Winter',
        AnimeSeason::SPRING => 'Spring',
        AnimeSeason::SUMMER => 'Summer',
        AnimeSeason::FALL => 'Fall',
    ],
    BalanceFrequency::class => [
        BalanceFrequency::ONCE => 'Once',
        BalanceFrequency::ANNUALLY => 'Annually',
        BalanceFrequency::BIANNUALLY => 'Biannually',
        BalanceFrequency::QUARTERLY => 'Quarterly',
        BalanceFrequency::MONTHLY => 'Monthly',
    ],
    ImageFacet::class => [
        ImageFacet::COVER_SMALL => 'Small Cover',
        ImageFacet::COVER_LARGE => 'Large Cover',
    ],
    InvitationStatus::class => [
        InvitationStatus::OPEN => 'Open',
        InvitationStatus::CLOSED => 'Closed',
    ],
    ResourceSite::class => [
        ResourceSite::OFFICIAL_SITE => 'Official Website',
        ResourceSite::TWITTER => 'Twitter',
        ResourceSite::ANIDB => 'aniDB',
        ResourceSite::ANILIST => 'AniList',
        ResourceSite::ANIME_PLANET => 'Anime-Planet',
        ResourceSite::ANN => 'Anime News Network',
        ResourceSite::KITSU => 'Kitsu',
        ResourceSite::MAL => 'MyAnimeList',
        ResourceSite::WIKI => 'Wiki',
    ],
    Service::class => [
        Service::OTHER => 'Other',
        Service::DIGITALOCEAN => 'DigitalOcean',
        Service::AWS => 'AWS',
        Service::HOVER => 'Hover',
        Service::WALKERSERVERS => 'WalkerServers',
    ],
    ThemeType::class => [
        ThemeType::OP => 'OP',
        ThemeType::ED => 'ED',
    ],
    VideoOverlap::class => [
        VideoOverlap::NONE => 'None',
        VideoOverlap::TRANS => 'Transition',
        VideoOverlap::OVER => 'Over',
    ],
    VideoSource::class => [
        VideoSource::WEB => 'WEB',
        VideoSource::RAW => 'RAW',
        VideoSource::BD => 'BD',
        VideoSource::DVD => 'DVD',
        VideoSource::VHS => 'VHS',
        VideoSource::LD => 'LD',
    ],
];
