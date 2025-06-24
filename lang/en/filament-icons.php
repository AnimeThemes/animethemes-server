<?php

declare(strict_types=1);

use Filament\Support\Icons\Heroicon;

return [
    'actions' => [
        'anime' => [
            'attach_streaming_resource' => 'heroicon-'.Heroicon::OutlinedTv->value,
            'backfill' => 'heroicon-'.Heroicon::OutlinedBars4->value,
            'discord_thread' => 'heroicon-'.Heroicon::OutlinedChatBubbleLeftRight->value,
        ],
        'base' => [
            'copied' => 'heroicon-'.Heroicon::OutlinedClipboard->value,
            'delete' => 'heroicon-m-'.Heroicon::Trash->value,
            'edit' => 'heroicon-'.Heroicon::OutlinedPencilSquare->value,
            'group_delete' => 'heroicon-'.Heroicon::OutlinedTrash->value,
            'move_all' => 'heroicon-'.Heroicon::OutlinedArrowLongRight->value,
        ],
        'models' => [
            'list' => [
                'sync_profile' => 'heroicon-'.Heroicon::OutlinedArrowPath->value,
            ],
            'wiki' => [
                'attach_image' => 'heroicon-'.Heroicon::OutlinedPhoto->value,
                'attach_resource' =>'heroicon-'.Heroicon::OutlinedQueueList->value,
            ],
        ],
        'storage' => [
            'move' => 'heroicon-'.Heroicon::OutlinedArrowLongRight->value,
        ],
        'video' => [
            'backfill' => 'heroicon-'.Heroicon::OutlinedSpeakerWave->value,
        ],
    ],
    'bulk_actions' => [
        'discord' => [
            'notification' =>'heroicon-'.Heroicon::OutlinedBell->value,
        ],
    ],
    'dashboards' => [
        'admin' => 'heroicon-m-'.Heroicon::ChartBarSquare->value,
        'dev' => 'heroicon-m-'.Heroicon::CodeBracket->value,
        'wiki' => 'heroicon-m-'.Heroicon::ChartBar->value,
    ],
    'fields' => [
        'user' => [
            'email' => 'heroicon-m-'.Heroicon::Envelope->value,
        ],
    ],
    'resources' => [
        'action_logs' => 'heroicon-'.Heroicon::OutlinedRectangleStack->value,
        'anime_synonyms' => 'heroicon-'.Heroicon::OutlinedGlobeAlt->value,
        'anime_theme_entries' => 'heroicon-'.Heroicon::OutlinedListBullet->value,
        'anime_themes' => 'heroicon-'.Heroicon::OutlinedListBullet->value,
        'anime' =>'heroicon-'.Heroicon::OutlinedTv->value,
        'announcements' => 'heroicon-'.Heroicon::OutlinedMegaphone->value,
        'artists' => 'heroicon-'.Heroicon::OutlinedUserCircle->value,
        'audios' => 'heroicon-'.Heroicon::OutlinedSpeakerWave->value,
        'discord_thread' => 'heroicon-'.Heroicon::OutlinedChatBubbleLeftRight->value,
        'dumps' => 'heroicon-'.Heroicon::OutlinedCircleStack->value,
        'external_entries' => 'heroicon-'.Heroicon::OutlinedQueueList->value,
        'external_profiles' => 'heroicon-'.Heroicon::OutlinedUser->value,
        'external_resources' => 'heroicon-'.Heroicon::OutlinedLink->value,
        'features' => 'heroicon-'.Heroicon::OutlinedCog6Tooth->value,
        'featured_themes' => 'heroicon-'.Heroicon::OutlinedCalendarDays->value,
        'groups' => 'heroicon-'.Heroicon::OutlinedFolderOpen->value,
        'images' => 'heroicon-'.Heroicon::OutlinedPhoto->value,
        'members' => '',
        'memberships' => 'heroicon-'.Heroicon::OutlinedListBullet->value,
        'pages' => 'heroicon-'.Heroicon::OutlinedDocumentText->value,
        'performances' => 'heroicon-'.Heroicon::OutlinedMusicalNote->value,
        'permissions' => 'heroicon-'.Heroicon::OutlinedInformationCircle->value,
        'playlist_tracks' => 'heroicon-'.Heroicon::OutlinedPlay->value,
        'playlists' => 'heroicon-'.Heroicon::OutlinedPlay->value,
        'reports' => 'heroicon-'.Heroicon::OutlinedLightBulb->value,
        'report_steps' => 'heroicon-'.Heroicon::OutlinedLightBulb->value,
        'roles' => 'heroicon-'.Heroicon::OutlinedBriefcase->value,
        'series' => 'heroicon-'.Heroicon::OutlinedFolder->value,
        'songs' => 'heroicon-'.Heroicon::OutlinedMusicalNote->value,
        'studios' => 'heroicon-'.Heroicon::OutlinedBuildingOffice->value,
        'users' => 'heroicon-'.Heroicon::OutlinedUsers->value,
        'video_scripts' => 'heroicon-'.Heroicon::OutlinedDocumentText->value,
        'videos' => 'heroicon-'.Heroicon::OutlinedVideoCamera->value,
    ],
    'table_actions' => [
        'base' => [
            'reconcile' => 'heroicon-'.Heroicon::OutlinedArrowPath->value,
            'prune' => 'heroicon-'.Heroicon::OutlinedTrash->value,
            'upload' => 'heroicon-'.Heroicon::OutlinedArrowUpTray->value,
        ],
        'discord_thread' => [
            'message' => [
                'edit' => 'heroicon-'.Heroicon::OutlinedPencilSquare->value,
                'send' => 'heroicon-'.Heroicon::OutlinedChatBubbleLeft->value,
            ],
        ],
        'dump' => [
            'dump' => 'heroicon-'.Heroicon::OutlinedCircleStack->value,
        ],
    ],
];