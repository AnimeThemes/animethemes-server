<?php

use App\Enums\AnimeSeason;
use App\Enums\BillingFrequency;
use App\Enums\BillingService;
use App\Enums\ImageFacet;
use App\Enums\InvitationStatus;
use App\Enums\ResourceSite;
use App\Enums\ThemeType;
use App\Enums\VideoOverlap;
use App\Enums\VideoSource;

return [
    AnimeSeason::class => [
        AnimeSeason::WINTER => 'Winter',
        AnimeSeason::SPRING => 'Spring',
        AnimeSeason::SUMMER => 'Summer',
        AnimeSeason::FALL => 'Fall',
    ],
    BillingFrequency::class => [
        BillingFrequency::ONCE => 'Once',
        BillingFrequency::ANNUALLY => 'Annually',
        BillingFrequency::BIANNUALLY => 'Biannually',
        BillingFrequency::QUARTERLY => 'Quarterly',
        BillingFrequency::MONTHLY => 'Monthly',
    ],
    BillingService::class => [
        BillingService::OTHER => 'Other',
        BillingService::DIGITALOCEAN => 'DigitalOcean',
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
