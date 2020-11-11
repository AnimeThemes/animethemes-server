<?php

use App\Enums\InvitationStatus;
use App\Enums\VideoOverlap;
use App\Enums\ResourceSite;
use App\Enums\AnimeSeason;
use App\Enums\VideoSource;
use App\Enums\ThemeType;
use App\Enums\UserRole;

return [
    InvitationStatus::class => [
        InvitationStatus::OPEN => 'Open',
        InvitationStatus::CLOSED => 'Closed',
    ],
    VideoOverlap::class => [
        VideoOverlap::NONE => 'None',
        VideoOverlap::TRANS => 'Transition',
        VideoOverlap::OVER => 'Over',
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
    AnimeSeason::class => [
        AnimeSeason::FALL => 'Fall',
        AnimeSeason::SUMMER => 'Summer',
        AnimeSeason::SPRING => 'Spring',
        AnimeSeason::WINTER => 'Winter',
    ],
    VideoSource::class => [
        VideoSource::WEB => 'WEB',
        VideoSource::RAW => 'RAW',
        VideoSource::BD => 'BD',
        VideoSource::DVD => 'DVD',
        VideoSource::VHS => 'VHS',
    ],
    ThemeType::class => [
        ThemeType::OP => 'OP',
        ThemeType::ED => 'ED',
    ],
    UserRole::class => [
        UserRole::READ_ONLY => 'Read Only',
        UserRole::CONTRIBUTOR => 'Contributor',
        UserRole::ADMIN => 'Admin',
    ],
];
