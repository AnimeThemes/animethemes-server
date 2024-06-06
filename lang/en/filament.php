<?php

declare(strict_types=1);

return [
    'actions' => [
        'anime' => [
            'backfill' => [
                'confirmButtonText' => 'Backfill',
                'fields' => [
                    'images' => [
                        'large_cover' => [
                            'help' => 'Use Anilist Resource to map Large Cover Image',
                            'name' => 'Backfill Large Cover',
                        ],
                        'name' => 'Backfill Images',
                        'small_cover' => [
                            'help' => 'Use Anilist Resource to map Small Cover Image',
                            'name' => 'Backfill Small Cover',
                        ],
                    ],
                    'resources' => [
                        'anidb' => [
                            'help' => 'Use the Manami Project Anime Offline Database hosted by yuna.moe to find an AniDB mapping from a MAL, Anilist or Kitsu Resource',
                            'name' => 'Backfill AniDB Resource',
                        ],
                        'anilist' => [
                            'help' => 'Use the MAL, Kitsu or AniDB Resource to find an Anilist mapping',
                            'name' => 'Backfill Anilist Resource',
                        ],
                        'ann' => [
                            'help' => 'Use the Kitsu resource to find an ANN mapping',
                            'name' => 'Backfill ANN Resource',
                        ],
                        'kitsu' => [
                            'help' => 'Use the Kitsu API to find a mapping from a MAL, Anilist, AniDB or ANN Resource',
                            'name' => 'Backfill Kitsu Resource',
                        ],
                        'mal' => [
                            'help' => 'Use the Kitsu, Anilist or AniDB Resource to find a MAL mapping',
                            'name' => 'Backfill MyAnimeList Resource',
                        ],
                        'external_links' => [
                            'help' => 'Use Anilist Resource to find other resources as Official Sites and Streamings',
                            'name' => 'Backfill Other Resources'
                        ],
                        'name' => 'Backfill Resources',
                    ],
                    'studios' => [
                        'anime' => [
                            'help' => 'Use the MAL, Anilist or Kitsu Resource to map Anime Studios',
                            'name' => 'Backfill Anime Studios',
                        ],
                        'name' => 'Backfill Studios',
                    ],
                    'synonyms' => [
                        'help' => 'Use the Anilist Resource to map Anime Synonyms',
                        'name' => 'Backfill Synonyms',
                    ],
                ],
                'message' => [
                    'resource_required_failure' => 'At least one Resource is required to backfill Anime',
                ],
                'name' => 'Backfill Anime',
            ],
            'discord' => [
                'thread' => [
                    'name' => 'Create Discord Thread',
                ],
            ],
        ],
        'audio' => [
            'delete' => [
                'confirmText' => 'Remove Audio from configured storage disks and from the database?',
                'name' => 'Remove Audio',
            ],
            'move' => [
                'name' => 'Move Audio',
            ],
            'upload' => [
                'name' => 'Upload Audio',
            ],
            'attach_related_videos' => [
                'name' => 'Attach Audio to Related Videos',
            ],
        ],
        'base' => [
            'cancelButtonText' => 'Cancel',
            'confirmButtonText' => 'Confirm',
            'copied' => 'Copied',
            'delete' => 'Delete',
            'detach' => 'Detach',
            'edit' => 'Edit',
            'forcedelete' => 'Force Delete',
            'restore' => 'Restore',
            'view' => 'View',
        ],
        'discord' => [
            'thread' => [
                'name' => 'Name',
                'help' => 'The name of the thread to be created. Use the default name or a shorter synonym if it exceeds 100 characters.'
            ]
        ],
        'dump' => [
            'dump' => [
                'confirmButtonText' => 'Dump',
                'fields' => [
                    'mysql' => [
                        'comments' => [
                            'help' => 'Add comments to dump file',
                            'name' => 'Comments',
                        ],
                        'default_character_set' => [
                            'help' => 'Specify default character set',
                            'name' => 'Default Character Set',
                        ],
                        'extended_insert' => [
                            'help' => 'Use multiple-row INSERT syntax',
                            'name' => 'Extended Insert',
                        ],
                        'lock_tables' => [
                            'help' => 'Lock all tables before dumping them',
                            'name' => 'Lock Tables',
                        ],
                        'no_create_info' => [
                            'help' => 'Do not write CREATE TABLE statements that re-create each dumped table',
                            'name' => 'No Create Info',
                        ],
                        'quick' => [
                            'help' => 'Retrieve rows for a table from the server a row at a time',
                            'name' => 'Quick',
                        ],
                        'set_gtid_purged' => [
                            'help' => 'Whether to add SET @@GLOBAL.GTID_PURGED to output',
                            'name' => 'Set GTID Purged',
                            'options' => [
                                'auto' => 'AUTO',
                                'off' => 'OFF',
                                'on' => 'ON',
                            ],
                        ],
                        'single_transaction' => [
                            'help' => 'Issue a BEGIN SQL statement before dumping data from server',
                            'name' => 'Single Transaction',
                        ],
                        'skip_column_statistics' => [
                            'help' => 'Do not add analyze table statements to generate histogram statistics',
                            'name' => 'Skip Column Statistics',
                        ],
                        'skip_comments' => [
                            'help' => 'Do not add comments to dump file',
                            'name' => 'Skip Comments',
                        ],
                        'skip_extended_insert' => [
                            'help' => 'Turn off extended-insert',
                            'name' => 'Skip Extended Insert',
                        ],
                        'skip_lock_tables' => [
                            'help' => 'Do not lock tables before dumping them',
                            'name' => 'Skip Lock Tables',
                        ],
                        'skip_quick' => [
                            'help' => 'Do not retrieve rows for a table from the server a row at a time',
                            'name' => 'Skip Quick',
                        ],
                    ],
                    'postgresql' => [
                        'data_only' => [
                            'help' => 'Dump only the data, not the schema (data definitions).',
                            'name' => 'Data Only',
                        ],
                        'inserts' => [
                            'help' => 'Dump data as INSERT commands (rather than COPY).',
                            'name' => 'Inserts',
                        ],
                    ],
                ],
                'name' => [
                    'document' => 'Dump Document Tables',
                    'wiki' => 'Dump Wiki Tables',
                ],
            ],
            'prune' => [
                'name' => 'Prune Dumps',
            ],
        ],
        'models' => [
            'assign_hashids' => [
                'name' => 'Assign Hashids',
                'confirmButtonText' => 'Assign',
            ],
            'wiki' => [
                'attach_resource' => [
                    'confirmButtonText' => 'Attach',
                    'fields' => [
                        'official_site' => [
                            'help' => 'Ex: https://kaguya.love/',
                        ],
                        'twitter' => [
                            'help' => 'Ex: https://twitter.com/AnimeThemesMoe',
                        ],
                        'anidb' => [
                            'help' => 'Ex: https://anidb.net/anime/11746, https://anidb.net/creator/10759',
                        ],
                        'anilist' => [
                            'help' => 'Ex: https://anilist.co/anime/21460, https://anilist.co/staff/106030',
                        ],
                        'anime_planet' => [
                            'help' => 'Ex: https://www.anime-planet.com/anime/sound-euphonium-2, https://www.anime-planet.com/people/chika-anzai',
                        ],
                        'ann' => [
                            'help' => 'Ex: https://www.animenewsnetwork.com/encyclopedia/anime.php?id=18558',
                        ],
                        'kitsu' => [
                            'help' => 'Ex: https://kitsu.io/anime/hibike-euphonium-2',
                        ],
                        'mal' => [
                            'help' => 'Ex: https://myanimelist.net/anime/31988, https://myanimelist.net/people/11030',
                        ],
                        'wiki' => [
                            'help' => 'Ex: https://unite-up.fandom.com/wiki/Protostar',
                        ],
                        'spotify' => [
                            'help' => 'Ex: https://open.spotify.com/track/5dmkAW2HpEJgFDSgkywm8N',
                        ],
                        'youtube_music' => [
                            'help' => 'Ex: https://music.youtube.com/watch?v=dHasNhuseU8',
                        ],
                        'youtube' => [
                            'help' => 'Ex: https://www.youtube.com/@liyuuchannel',
                        ],
                        'apple_music' => [
                            'help' => 'Ex: https://music.apple.com/jp/album/1711324281',
                        ],
                        'amazon_music' => [
                            'help' => 'Ex: https://music.amazon.co.jp/tracks/B0CKVQGSJY',
                        ],
                        'crunchyroll' => [
                            'help' => 'Ex: https://www.crunchyroll.com/series/GRDQNQW9Y',
                        ],
                        'hidive' => [
                            'help' => 'Ex: https://www.hidive.com/tv/the-eminence-in-shadow',
                        ],
                        'netflix' => [
                            'help' => 'Ex: https://www.netflix.com/title/81564905',
                        ],
                        'disney_plus' => [
                            'help' => 'Ex: https://www.disneyplus.com/series/tokyo-revengers/4HFbN55sAh0i',
                        ],
                        'hulu' => [
                            'help' => 'Ex: https://www.hulu.com/series/the-eminence-in-shadow-66f37cf4-dba5-4511-ae26-e4092df1668b',
                        ],
                        'amazon_prime_video' => [
                            'help' => 'Ex: https://www.primevideo.com/detail/0PXZCO5NGDNH8OWTJIDTEB8IEF',
                        ],
                    ],
                    'name' => 'Attach Resources',
                ],
                'attach_streaming_resource' => [
                    'name' => 'Attach Streaming Resources',
                ],
                'attach_image' => [
                    'confirmButtonText' => 'Upload',
                    'name' => 'Attach Images'
                ],
                'upload_image' => [
                    'name' => 'Upload Image',
                ],
            ],
        ],
        'permission' => [
            'give_role' => [
                'name' => 'Give Role',
            ],
            'revoke_role' => [
                'name' => 'Revoke Role',
            ],
        ],
        'repositories' => [
            'confirmButtonText' => 'Reconcile',
            'name' => 'Reconcile :label',
            'storage' => [
                'fields' => [
                    'path' => [
                        'help' => 'The directory to reconcile. Ex: 2022/Spring/.',
                        'name' => 'Path',
                    ],
                ],
            ],
        ],
        'role' => [
            'give_permission' => [
                'name' => 'Give Permission',
            ],
            'revoke_permission' => [
                'name' => 'Revoke Permission',
            ],
        ],
        'storage' => [
            'delete' => [
                'confirmButtonText' => 'Remove',
            ],
            'move' => [
                'confirmButtonText' => 'Move',
                'fields' => [
                    'path' => [
                        'help' => 'The new location of the file. Ex: 2009/Summer/Bakemonogatari-OP1.webm.',
                        'name' => 'Path',
                    ],
                ],
            ],
            'prune' => [
                'confirmButtonText' => 'Prune',
                'fields' => [
                    'hours' => [
                        'help' => 'Files last modified before the specified time in hours before the present time will be deleted.',
                        'name' => 'Hours',
                    ],
                ],
            ],
            'upload' => [
                'confirmButtonText' => 'Upload',
                'fields' => [
                    'file' => [
                        'help' => 'The file to upload. Files will be uploaded to each configured storage disk.',
                        'name' => 'File',
                    ],
                    'path' => [
                        'help' => 'The directory the file will be uploaded to. Ex: 2022/Spring.',
                        'name' => 'Path',
                    ],
                ],
            ],
        ],
        'studio' => [
            'backfill' => [
                'confirmButtonText' => 'Backfill',
                'fields' => [
                    'images' => [
                        'large_cover' => [
                            'help' => 'Use MAL Resource to map Large Cover Image',
                            'name' => 'Backfill Large Cover',
                        ],
                        'name' => 'Backfill Images',
                    ],
                ],
                'message' => [
                    'resource_required_failure' => 'At least one Resource is required to backfill Studio',
                ],
                'name' => 'Backfill Studio',
            ],
        ],
        'user' => [
            'give_permission' => [
                'name' => 'Give Permission',
            ],
            'give_role' => [
                'name' => 'Give Role',
            ],
            'revoke_permission' => [
                'name' => 'Revoke Permission',
            ],
            'revoke_role' => [
                'name' => 'Revoke Role',
            ],
        ],
        'video_script' => [
            'delete' => [
                'confirmText' => 'Remove Video Script from configured storage disks and from the database?',
                'name' => 'Remove Video Script',
            ],
            'move' => [
                'name' => 'Move Video Script',
            ],
            'upload' => [
                'name' => 'Upload Video Script',
            ],
        ],
        'video' => [
            'backfill' => [
                'confirmButtonText' => 'Backfill',
                'fields' => [
                    'derive_source' => [
                        'help' => 'If Yes, use the source Video to backfill Audio. If No, use this Video to backfill Audio. Yes should be used in most cases. No is useful for outlier videos where we may want an additional Audio to represent the song like a second verse or an SFX version.',
                        'name' => 'Derive Source Video',
                    ],
                    'overwrite' => [
                        'help' => 'If Yes, the Audio will be extracted from the Video even if the Audio already exists. If No, the Audio will only be extracted from the Video if the Audio doesn\'t exist. No should be used in most cases. Yes is useful if we are replacing Audio for a Video.',
                        'name' => 'Overwrite Audio',
                    ],
                ],
                'name' => 'Backfill Audio',
            ],
            'delete' => [
                'confirmText' => 'Remove Video from configured storage disks and from the database?',
                'name' => 'Remove Video',
            ],
            'move' => [
                'name' => 'Move Video',
            ],
            'upload' => [
                'name' => 'Upload Video',
            ],
        ],
    ],
    'bulk_actions' => [
        'base' => [
            'delete' => 'Delete Selected',
            'detach' => 'Detach Selected',
            'forcedelete' => 'Force Delete Selected',
            'restore' => 'Restore Selected',
        ],
        'discord' => [
            'notification' => [
                'icon' => 'heroicon-o-bell',
                'name' => 'Create Discord Notification',
                'type' => [
                    'help' => 'Are they new videos or replacement?',
                    'name' => 'Type',
                    'options' => [
                        'added' => 'Added',
                        'updated' => 'Updated',
                    ],
                ],
            ],
        ],
    ],
    'dashboards' => [
        'icon' => [
            'admin' => 'heroicon-m-chart-bar',
            'wiki' => 'heroicon-m-chart-bar',
        ],
        'label' => [
            'admin' => 'Admin',
            'wiki' => 'Wiki',
        ],
    ],
    'fields' => [
        'anime_synonym' => [
            'text' => [
                'help' => 'For alternative titles, licensed titles, common abbreviations and/or shortenings',
                'name' => 'Text',
            ],
            'type' => [
                'help' => 'The title type.',
                'name' => 'Type',
            ]
        ],
        'anime_theme_entry' => [
            'episodes' => [
                'help' => 'The range(s) of episodes that the theme entry is used. Can be left blank if used for all episodes or if there are not episodes as with movies. Ex: "1-", "1-11", "1-2, 10, 12".',
                'name' => 'Episodes',
            ],
            'notes' => [
                'help' => 'Any additional information not included in other fields that may be useful',
                'name' => 'Notes',
            ],
            'nsfw' => [
                'help' => 'Does the entry include Not Safe For Work content? Set at your discretion. There will not be rigid guidelines to define when this property should be set.',
                'name' => 'NSFW',
            ],
            'spoiler' => [
                'help' => 'Does the entry include content that spoils the show? You may also include up to which episode is spoiled in Notes (Ex: Ep 6 spoilers).',
                'name' => 'Spoiler',
            ],
            'version' => [
                'help' => 'The Version number of the Theme. Can be left blank if there is only one version. Version is only required if there exist at least 2 in the sequence.',
                'name' => 'Version',
            ],
        ],
        'anime_theme' => [
            'sequence' => [
                'help' => 'Numeric ordering of theme. If only one theme of this type exists for the show, this can be left blank.',
                'name' => 'Sequence',
            ],
            'slug' => [
                'help' => 'Used as the URL Slug / Model Route Key. By default, this should be the Type and Sequence lowercased and "-" replacing spaces. These should be unique within the scope of the anime. Ex: "OP1", "ED1", "OP2-Dub".',
                'name' => 'Slug',
            ],
            'type' => [
                'help' => 'Is this an OP or an ED?',
                'name' => 'Type',
            ],
        ],
        'anime' => [
            'name' => [
                'help' => 'The display title of the Anime. By default, we will use the same title as MAL. Ex: "Bakemonogatari", "Code Geass: Hangyaku no Lelouch", "Dungeon ni Deai wo Motomeru no wa Machigatteiru Darou ka".',
                'name' => 'Name',
            ],
            'season' => [
                'help' => 'The Season in which the Anime premiered. By default, we will use the Premiered Field on the MAL page.',
                'name' => 'Season',
            ],
            'slug' => [
                'help' => 'Used as the URL Slug / Model Route Key. By default, this should be the Name lowercased and "_" replacing spaces. Shortenings/Abbreviations are also accepted. Ex: "monogatari", "code_geass", "danmachi".',
                'name' => 'Slug',
            ],
            'synopsis' => [
                'help' => 'The brief description of the Anime',
                'name' => 'Synopsis',
            ],
            'year' => [
                'help' => 'The Year in which the Anime premiered. By default, we will use the Premiered Field on the MAL page.',
                'name' => 'Year',
            ],
            'media_format' => [
                'help' => 'The Format of the Anime. By default, we will use the Type Field on the MAL page.',
                'name' => 'Media Format'
            ],
            'resources' => [
                'as' => [
                    'help' => 'Used to distinguish resources that map to the same anime. For example, Aware! Meisaku-kun has one MAL page and many aniDB pages.',
                    'name' => 'As',
                ],
            ],
        ],
        'artist' => [
            'groups' => [
                'as' => [
                    'help' => 'Used in place of the Artist name if the performance is made as a character or group/unit member.',
                    'name' => 'As',
                ],
            ],
            'members' => [
                'as' => [
                    'help' => 'Used in place of the Artist name if the performance is made as a character or group/unit member',
                    'name' => 'As',
                ],
            ],
            'name' => [
                'help' => 'The display title of the Artist. By default, we will use the same title as MAL, but we will prefer "[Given Name] [Family name]". Ex: "Aimer", "Yui Horie", "Fear, and Loathing in Las Vegas".',
                'name' => 'Name',
            ],
            'resources' => [
                'as' => [
                    'help' => 'Used to distinguish resources that map to the same artist. For example, the OxT music unit has a dedicated AnidB page but ANN does not.',
                    'name' => 'As',
                ],
            ],
            'slug' => [
                'help' => 'Used as the URL Slug / Model Route Key. By default, this should be the Name lowercased and "_" replacing spaces. Shortenings/Abbreviations are also accepted. Ex: "aimer", "yui_horie", "falilv"',
                'name' => 'Slug',
            ],
            'songs' => [
                'as' => [
                    'help' => 'Used in place of the Artist name if the performance is made as a character or group/unit member.',
                    'name' => 'As',
                ],
            ],
        ],
        'announcement' => [
            'content' => 'Content',
        ],
        'audio' => [
            'basename' => [
                'name' => 'Basename',
            ],
            'filename' => [
                'name' => 'Filename',
            ],
            'mimetype' => [
                'name' => 'MIME Type',
            ],
            'path' => [
                'name' => 'Path',
            ],
            'size' => [
                'name' => 'Size',
            ],
        ],
        'base' => [
            'created_at' => 'Created At',
            'deleted_at' => 'Deleted At',
            'file_properties' => 'File Properties',
            'id' => 'ID',
            'timestamps' => 'Timestamps',
            'updated_at' => 'Updated At',
        ],
        'discord_thread' => [
            'id' => [
                'help' => 'The thread ID on Discord',
                'name' => 'Thread ID',
            ],
            'name' => [
                'help' => 'The name of the thread on Discord',
                'name' => 'Name',
            ],
        ],
        'dump' => [
            'path' => 'Path',
        ],
        'external_resource' => [
            'external_id' => [
                'help' => 'The identifier used by the external site.',
                'name' => 'External ID',
            ],
            'link' => [
                'help' => 'The URL of the resource. Ex: https://myanimelist.net/people/8/, https://anidb.net/creator/3/, https://kaguya.love/',
                'name' => 'Link',
            ],
            'site' => [
                'help' => 'The site that we are linking to.',
                'name' => 'Site',
            ],
        ],
        'feature' => [
            'key' => [
                'help' => 'The name of the feature. Class strings label features that require business logic to resolve. Otherwise, this is global value.',
                'name' => 'Key',
            ],
            'value' => [
                'help' => 'Binary state features should be true or false. Rich Feature values can be any string.',
                'name' => 'Value',
            ],
        ],
        'featured_theme' => [
            'end_at' => [
                'help' => 'The datetime that the featured theme should stop being featured.',
                'name' => 'End At',
            ],
            'start_at' => [
                'help' => 'The datetime that the featured theme should start being featured.',
                'name' => 'Start At',
            ],
        ],
        'group' => [
            'name' => [
                'name' => 'Name',
                'help' => 'The name of the group.',
            ],
            'slug' => [
                'name' => 'Slug',
                'help' => 'The slug that will be appended to the slug of the theme that has the group.',
            ],
        ],
        'image' => [
            'facet' => [
                'help' => 'The page component that the image is intended for. Example: Is this a small cover image or a large cover image?',
                'name' => 'Facet',
            ],
            'image' => [
                'name' => 'Image',
            ],
            'path' => [
                'name' => 'Path',
            ],
        ],
        'page' => [
            'body' => [
                'help' => 'The content of the Page.',
                'name' => 'Body',
            ],
            'name' => [
                'help' => 'The display title of the Page.',
                'name' => 'Name',
            ],
            'slug' => [
                'help' => 'Used as the URL Slug / Model Route Key. By default, this should be the Name lowercased and "_" replacing spaces.',
                'name' => 'Slug',
            ],
        ],
        'permission' => [
            'name' => 'Name',
        ],
        'playlist_track' => [
            'hashid' => [
                'help' => 'The short, unique, non-sequential id derived from model identifiers.',
                'name' => 'Hashid',
            ],
            'next' => [
                'help' => 'The next Track in the Playlist',
                'name' => 'Next Track',
            ],
            'previous' => [
                'help' => 'The previous Track in the Playlist',
                'name' => 'Previous Track',
            ],
        ],
        'playlist' => [
            'first' => [
                'help' => 'The first Track of the Playlist',
                'name' => 'First Track',
            ],
            'hashid' => [
                'help' => 'The short, unique, non-sequential id derived from model identifiers.',
                'name' => 'Hashid',
            ],
            'last' => [
                'help' => 'The last Track of the Playlist',
                'name' => 'Last Track',
            ],
            'name' => [
                'help' => 'The display title of the Playlist',
                'name' => 'Name',
            ],
            'description' => [
                'help' => 'The description of the Playlist',
                'name' => 'Description',
            ],
            'visibility' => [
                'help' => 'Who can view this playlist? Private: only the owner. Unlisted: anyone directly linked to the playlist. Public: anyone can search for the playlist.',
                'name' => 'Visibility',
            ],
        ],
        'role' => [
            'color' => [
                'help' => 'The color that will be used on the profile screen to designate the user.',
                'name' => 'Color',
            ],
            'default' => [
                'help' => 'Should this role be assigned to new users upon email verification?',
                'name' => 'Default',
            ],
            'name' => 'Name',
            'priority' => [
                'help' => 'The weight assigned to a role. Higher numbers are higher priority.',
                'name' => 'Priority',
            ],
        ],
        'series' => [
            'name' => [
                'help' => 'The display title of the Series. Ex: "Monogatari", "Code Geass", "Dungeon ni Deai wo Motomeru no wa Machigatteiru Darou ka".',
                'name' => 'Name',
            ],
            'slug' => [
                'help' => 'Used as the URL Slug / Model Route Key. By default, this should be the Name lowercased and "_" replacing spaces. Shortenings/Abbreviations are also accepted. Ex: "monogatari", "code_geass", "danmachi".',
                'name' => 'Slug',
            ],
        ],
        'song' => [
            'title' => [
                'help' => 'The title of the song',
                'name' => 'Title',
            ],
            'resources' => [
                'as' => [
                    'help' => 'Used to distinguish resources that map to the same song.',
                    'name' => 'As'
                ]
            ]
        ],
        'studio' => [
            'name' => [
                'help' => 'The display title of the Studio',
                'name' => 'Name',
            ],
            'resources' => [
                'as' => [
                    'help' => 'Used to distinguish resources that map to the same studio.',
                    'name' => 'As',
                ],
            ],
            'slug' => [
                'help' => 'Used as the URL Slug / Model Route Key. By default, this should be the Name lowercased and "_" replacing spaces. Shortenings/Abbreviations are also accepted.',
                'name' => 'Slug',
            ],
        ],
        'user' => [
            'avatar' => 'Avatar',
            'email' => 'Email',
            'name' => 'Name',
        ],
        'video_script' => [
            'path' => 'Path',
        ],
        'video' => [
            'basename' => [
                'name' => 'Basename',
            ],
            'filename' => [
                'name' => 'Filename',
            ],
            'lyrics' => [
                'help' => 'Set if this video has subtitles for song lyrics.',
                'name' => 'Lyrics',
            ],
            'mimetype' => [
                'name' => 'MIME Type',
            ],
            'nc' => [
                'help' => 'Set if this video is creditless.',
                'name' => 'NC',
            ],
            'overlap' => [
                'help' => 'The degree to which the sequence and episode content overlap. None: No overlap. Transition: partial overlap. Over: full overlap.',
                'name' => 'Overlap',
            ],
            'path' => [
                'name' => 'Path',
            ],
            'resolution' => [
                'help' => 'Frame height of the video',
                'name' => 'Resolution',
            ],
            'size' => [
                'name' => 'Size',
            ],
            'source' => [
                'help' => 'Where did this video come from?',
                'name' => 'Source',
            ],
            'subbed' => [
                'help' => 'Set if this video has subtitles of dialogue.',
                'name' => 'Subbed',
            ],
            'uncen' => [
                'help' => 'Set if this video is an uncensored version of a censored sequence.',
                'name' => 'Uncensored',
            ],
        ],
    ],
    'filters' => [
        'anime' => [
            'year_from' => 'Year - From',
            'year_to' => 'Year - To',
        ],
        'anime_theme' => [
            'sequence_from' => 'Sequence - From',
            'sequence_to' => 'Sequence - To',
        ],
        'anime_theme_entry' => [
            'version_from' => 'Version - From',
            'version_to' => 'Version - To',
        ],
        'audio' => [
            'size_from' => 'Size - From',
            'size_to' => 'Size - To'
        ],
        'base' => [
            'created_at_from' => 'Created At - From',
            'created_at_to' => 'Created At - To',
            'updated_at_from' => 'Updated At - From',
            'updated_at_to' => 'Updated At - To',
            'deleted_at_from' => 'Deleted At - From',
            'deleted_at_to' => 'Deleted At - To',
        ],
        'external_resource' => [
            'external_id_from' => 'External ID - From',
            'external_id_to' => 'External ID - To',
        ],
        'role' => [
            'priority_from' => 'Priority - From',
            'priority_to' => 'Priority - To',
        ],
        'video' => [
            'resolution_from' => 'Resolution - From',
            'resolution_to' => 'Resolution - To',
            'size_from' => 'Size - From',
            'size_to' => 'Size - To'
        ],
    ],
    'resources' => [
        'group' => [
            'admin' => 'Admin',
            'auth' => 'Auth',
            'discord' => 'Discord',
            'document' => 'Document',
            'list' => 'List',
            'wiki' => 'Wiki',
        ],
        'icon' => [
            'anime_synonyms' => 'heroicon-o-globe-alt',
            'anime_theme_entries' => 'heroicon-o-list-bullet',
            'anime_themes' => 'heroicon-o-list-bullet',
            'anime' => 'heroicon-o-tv',
            'announcements' => 'heroicon-o-megaphone',
            'artists' => 'heroicon-o-user-circle',
            'audios' => 'heroicon-o-speaker-wave',
            'discord_thread' => 'heroicon-o-chat-bubble-left-right',
            'dumps' => 'heroicon-o-circle-stack',
            'external_resources' => 'heroicon-o-arrow-top-right-on-square',
            'features' => 'heroicon-o-cog-6-tooth',
            'featured_themes' => 'heroicon-o-calendar-days',
            'groups' => 'heroicon-o-folder-open',
            'images' => 'heroicon-o-photo',
            'members' => '',
            'pages' => 'heroicon-o-document-text',
            'permissions' => 'heroicon-o-information-circle',
            'playlist_tracks' => 'heroicon-o-play',
            'playlists' => 'heroicon-o-play',
            'roles' => 'heroicon-o-briefcase',
            'series' => 'heroicon-o-folder',
            'songs' => 'heroicon-o-musical-note',
            'studios' => 'heroicon-o-building-office',
            'users' => 'heroicon-o-users',
            'video_scripts' => 'heroicon-o-document-text',
            'videos' => 'heroicon-o-film',
        ],
        'label' => [
            'anime_synonyms' => 'Anime Synonyms',
            'anime_theme_entries' => 'Anime Theme Entries',
            'anime_themes' => 'Anime Themes',
            'anime' => 'Anime',
            'announcements' => 'Announcements',
            'artists' => 'Artists',
            'audios' => 'Audios',
            'discord_threads' => 'Threads',
            'dumps' => 'Dumps',
            'external_resources' => 'External Resources',
            'features' => 'Features',
            'featured_themes' => 'Featured Themes',
            'groups' => 'Groups',
            'images' => 'Images',
            'members' => 'Members',
            'pages' => 'Pages',
            'permissions' => 'Permissions',
            'playlist_tracks' => 'Playlist Tracks',
            'playlists' => 'Playlists',
            'roles' => 'Roles',
            'series' => 'Series',
            'songs' => 'Songs',
            'studios' => 'Studios',
            'users' => 'Users',
            'video_scripts' => 'Video Scripts',
            'videos' => 'Videos',
        ],
        'singularLabel' => [
            'anime_synonym' => 'Anime Synonym',
            'anime_theme_entry' => 'Anime Theme Entry',
            'anime_theme' => 'Anime Theme',
            'anime' => 'Anime',
            'announcement' => 'Announcement',
            'artist' => 'Artist',
            'audio' => 'Audio',
            'discord_thread' => 'Thread',
            'dump' => 'Dump',
            'external_resource' => 'External Resource',
            'feature' => 'Feature',
            'featured_theme' => 'Featured Theme',
            'group' => 'Group',
            'image' => 'Image',
            'member' => 'Member',
            'page' => 'Page',
            'permission' => 'Permission',
            'playlist_track' => 'Playlist Track',
            'playlist' => 'Playlist',
            'role' => 'Role',
            'series' => 'Series',
            'song' => 'Song',
            'studio' => 'Studio',
            'user' => 'User',
            'video_script' => 'Video Script',
            'video' => 'Video',
        ],
    ],
    'table_actions' => [
        'base' => [
            'reconcile' => [
                'icon' => 'heroicon-o-arrow-path',
            ],
            'upload' => [
                'icon' => 'heroicon-o-arrow-up-tray',
            ],
        ],
        'discord_thread' => [
            'message' => [
                'channelId' => [
                    'help' => 'The channel ID the message should be sent to',
                    'name' => 'Channel ID',
                ],
                'content' => [
                    'help' => 'The content of the message',
                    'name' => 'Content',
                ],
                'embeds' => [
                    'help' => 'The embeds of the message',
                    'name' => 'Embeds',
                    'body' => [
                        'title' => [
                            'help' => 'The title of the embed',
                            'name' => 'Title',
                        ],
                        'description' => [
                            'help' => 'The description of the embed',
                            'name' => 'Description',
                        ],
                        'color' => [
                            'help' => 'The color of the embed',
                            'name' => 'Color',
                        ],
                        'fields' => [
                            'title' => [
                                'help' => 'The fields of the embed',
                                'name' => 'Fields',
                            ],
                            'name' => [
                                'help' => 'The name of the field',
                                'name' => 'Name',
                            ],
                            'value' => [
                                'help' => 'The value of the field',
                                'name' => 'Value',
                            ],
                            'inline' => [
                                'help' => 'If the field should be inline',
                                'name' => 'Inline',
                            ],
                        ],
                    ],
                ],
                'url' => [
                    'help' => 'Click on "Copy Message Link" on Discord',
                    'name' => 'URL',
                ],
            ],
        ],
        'dump' => [
            'dump' => [
                'icon' => 'heroicon-o-circle-stack',
            ],
            'prune' => [
                'icon' => 'heroicon-o-trash',
            ],
        ],
    ],
    'tabs' => [
        'anime' => [
            'images' => [
                'name' => 'Without :facet Image',
            ],
            'resources' => [
                'name' => 'Without :site Resource',
            ],
            'streaming_resources' => [
                'name' => 'Without Streaming Resources',
            ],
            'studios' => [
                'name' => 'Without Studios',
            ],
        ],
        'artist' => [
            'images' => [
                'name' => 'Without :facet Image',
            ],
            'resources' => [
                'name' => 'Without :site Resource',
            ],
            'songs' => [
                'name' => 'Without Songs',
            ],
        ],
        'audio' => [
            'video' => [
                'name' => 'Without Video',
            ],
        ],
        'external_resource' => [
            'unlinked' => [
                'name' => 'Without Anime or Artist or Song or Studio',
            ],
        ],
        'image' => [
            'unlinked' => [
                'name' => 'Without Anime or Artist or Studio',
            ],
        ],
        'song' => [
            'artist' => [
                'name' => 'Without Artists',
            ],
            'resources' => [
                'name' => 'Without :site Resource',
            ]
        ],
        'studio' => [
            'images' => [
                'name' => 'Without :facet Image',
            ],
            'resources' => [
                'name' => 'Without :site Resource',
            ],
            'unlinked' => [
                'name' => 'Without Anime',
            ],
        ],
        'video' => [
            'audio' => [
                'name' => 'Without Audio',
            ],
            'resolution' => [
                'name' => 'With Unset Resolution',
            ],
            'script' => [
                'name' => 'Without Script',
            ],
            'source' => [
                'name' => 'With Unknown Source Type',
            ],
            'unlinked' => [
                'name' => 'Without Entries',
            ],
        ],
    ],
    'widgets' => [
        'month' => [
            'jan' => 'Jan',
            'feb' => 'Feb',
            'mar' => 'Mar',
            'apr' => 'Apr',
            'may' => 'May',
            'jun' => 'Jun',
            'jul' => 'Jul',
            'aug' => 'Aug',
            'sep' => 'Sep',
            'oct' => 'Oct',
            'nov' => 'Nov',
            'dec' => 'Dec',
        ],
    ],
];
