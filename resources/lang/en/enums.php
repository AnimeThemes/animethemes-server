<?php

use App\Enums\OverlapType;
use App\Enums\ResourceType;
use App\Enums\Season;
use App\Enums\SourceType;
use App\Enums\ThemeType;
use App\Enums\UserType;

return [
    OverlapType::class => [
        OverlapType::NONE => 'None',
        OverlapType::TRANS => 'Transition',
        OverlapType::OVER => 'Over',
    ],
    ResourceType::class => [
        ResourceType::OFFICIAL_SITE => 'Official Website',
        ResourceType::TWITTER => 'Twitter',
        ResourceType::ANIDB => 'aniDB',
        ResourceType::ANILIST => 'AniList',
        ResourceType::ANIME_PLANET => 'Anime-Planet',
        ResourceType::ANN => 'Anime News Network',
        ResourceType::KITSU => 'Kitsu',
        ResourceType::MAL => 'MyAnimeList',
        ResourceType::WIKI => 'Wiki',
    ],
    Season::class => [
        Season::FALL => 'Fall',
        Season::SUMMER => 'Summer',
        Season::SPRING => 'Spring',
        Season::WINTER => 'Winter',
    ],
    SourceType::class => [
        SourceType::WEB => 'WEB',
        SourceType::RAW => 'RAW',
        SourceType::BD => 'BD',
        SourceType::DVD => 'DVD',
        SourceType::VHS => 'VHS',
    ],
    ThemeType::class => [
        ThemeType::OP => 'OP',
        ThemeType::ED => 'ED',
    ],
    UserType::class => [
        UserType::READ_ONLY => 'Read Only',
        UserType::CONTRIBUTOR => 'Contributor',
        UserType::ADMIN => 'Admin',
    ],
];
