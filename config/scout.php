<?php

declare(strict_types=1);

use App\Models\List\ExternalProfile;
use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Video;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Search Engine
    |--------------------------------------------------------------------------
    |
    | This option controls the default search connection that gets used while
    | using Laravel Scout. This connection is used when syncing all models
    | to the search service. You should adjust this based on your needs.
    |
    | Supported: "algolia", "meilisearch", "database", "collection", "null"
    |
    */

    'driver' => env('SCOUT_DRIVER', 'algolia'),

    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    |
    | Here you may specify a prefix that will be applied to all search index
    | names used by Scout. This prefix may be useful if you have multiple
    | "tenants" or applications sharing the same search infrastructure.
    |
    */

    'prefix' => env('SCOUT_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Queue Data Syncing
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if the operations that sync your data
    | with your search engines are queued. When this is set to "true" then
    | all automatic data syncing will get queued for better performance.
    |
    */

    'queue' => env('SCOUT_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Database Transactions
    |--------------------------------------------------------------------------
    |
    | This configuration option determines if your data will only be synced
    | with your search indexes after every open database transaction has
    | been committed, thus preventing any discarded data from syncing.
    |
    */

    'after_commit' => true,

    /*
    |--------------------------------------------------------------------------
    | Chunk Sizes
    |--------------------------------------------------------------------------
    |
    | These options allow you to control the maximum chunk size when you are
    | mass importing data into the search engine. This allows you to fine
    | tune each of these chunk sizes based on the power of the servers.
    |
    */

    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Soft Deletes
    |--------------------------------------------------------------------------
    |
    | This option allows to control whether to keep soft deleted records in
    | the search indexes. Maintaining soft deleted records can be useful
    | if your application still needs to search for the records later.
    |
    */

    'soft_delete' => false,

    /*
    |--------------------------------------------------------------------------
    | Identify User
    |--------------------------------------------------------------------------
    |
    | This option allows you to control whether to notify the search engine
    | of the user performing the search. This is sometimes useful if the
    | engine supports any analytics based on this application's users.
    |
    | Supported engines: "algolia"
    |
    */

    'identify' => env('SCOUT_IDENTIFY', false),

    /*
    |--------------------------------------------------------------------------
    | Algolia Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Algolia settings. Algolia is a cloud hosted
    | search engine which works great with Scout out of the box. Just plug
    | in your application ID and admin API key to get started searching.
    |
    */

    'algolia' => [
        'id' => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Meilisearch Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Meilisearch settings. Meilisearch is an open
    | source search engine with minimal configuration. Below, you can state
    | the host and key information for your own Meilisearch installation.
    |
    | See: https://docs.meilisearch.com/guides/advanced_guides/configuration.html
    |
    */

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY'),
        'index-settings' => [
            // 'users' => [
            //     'filterableAttributes'=> ['id', 'name', 'email'],
            // ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Typesense Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Typesense settings. Typesense is an open
    | source search engine using minimal configuration. Below, you will
    | state the host, key, and schema configuration for the instance.
    |
    */

    'typesense' => [
        'client-settings' => [
            'api_key' => env('TYPESENSE_API_KEY', 'xyz'),
            'nodes' => [
                [
                    'host' => env('TYPESENSE_HOST', 'localhost'),
                    'port' => env('TYPESENSE_PORT', '8108'),
                    'path' => env('TYPESENSE_PATH', ''),
                    'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
                ],
            ],
            'nearest_node' => [
                'host' => env('TYPESENSE_HOST', 'localhost'),
                'port' => env('TYPESENSE_PORT', '8108'),
                'path' => env('TYPESENSE_PATH', ''),
                'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
            ],
            'connection_timeout_seconds' => env('TYPESENSE_CONNECTION_TIMEOUT_SECONDS', 2),
            'healthcheck_interval_seconds' => env('TYPESENSE_HEALTHCHECK_INTERVAL_SECONDS', 30),
            'num_retries' => env('TYPESENSE_NUM_RETRIES', 3),
            'retry_interval_seconds' => env('TYPESENSE_RETRY_INTERVAL_SECONDS', 1),
        ],
        'model-settings' => [
            ExternalProfile::class => [
                'collection-schema' => [
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'name',
                            'type' => 'string',
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => 'name',
                ],
            ],
            Playlist::class => [
                'collection-schema' => [
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'name',
                            'type' => 'string',
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => 'name',
                ],
            ],
            Anime::class => [
                'collection-schema' => [
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'name',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'season',
                            'type' => 'string',
                            'optional' => true,
                        ],
                        [
                            'name' => 'year',
                            'type' => 'int32',
                        ],
                        [
                            'name' => 'created_at',
                            'type' => 'int64',
                            'optional' => true,
                        ],
                        [
                            'name' => 'updated_at',
                            'type' => 'int64',
                            'optional' => true,
                        ],
                        [
                            'name' => '__soft_deleted',
                            'type' => 'int32',
                            'optional' => true,
                        ],
                        [
                            'name' => 'synonyms',
                            'type' => 'string[]',
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => 'name,synonyms',
                ],
            ],
            AnimeTheme::class => [
                'collection-schema' => [
                    'enable_nested_fields' => true,
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'created_at',
                            'type' => 'int64',
                            'optional' => true,
                        ],
                        [
                            'name' => 'type',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'sequence',
                            'type' => 'string',
                            'optional' => true,
                        ],
                        [
                            'name' => 'type_sequence',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'anime',
                            'type' => 'object',
                        ],
                        [
                            'name' => 'song',
                            'type' => 'object',
                            'optional' => true,
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => implode(',', [
                        'song.title',
                        'song.title_native',
                        'anime.name',
                        'anime.synonyms',
                        'type_sequence',
                    ]),
                    'query_by_weights' => '10,8,6,5,5',
                ],
            ],
            AnimeThemeEntry::class => [
                'collection-schema' => [
                    'enable_nested_fields' => true,
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'created_at',
                            'type' => 'int64',
                            'optional' => true,
                        ],
                        [
                            'name' => 'version',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'type_sequence_version',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'animetheme',
                            'type' => 'object',
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => implode(',', [
                        'animetheme.song.title',
                        'animetheme.song.title_native',
                        'animetheme.anime.name',
                        'animetheme.anime.synonyms',
                        'type_sequence_version',
                    ]),
                    'query_by_weights' => '10,8,6,5,5',
                ],
            ],
            Artist::class => [
                'collection-schema' => [
                    'enable_nested_fields' => true,
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'name',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'synonyms',
                            'type' => 'string[]',
                        ],
                        [
                            'name' => 'as',
                            'type' => 'string[]',
                        ],
                        [
                            'name' => 'created_at',
                            'type' => 'int64',
                            'optional' => true,
                        ],
                        [
                            'name' => 'search_text',
                            'type' => 'string',
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => implode(',', [
                        'name',
                        'synonyms',
                        'as',
                        'search_text',
                    ]),
                    'query_by_weights' => '10,8,3,1',
                    'text_match_type' => 'sum_score',
                ],
            ],
            Series::class => [
                'collection-schema' => [
                    'enable_nested_fields' => true,
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'name',
                            'type' => 'string',
                            'sort' => true,
                        ],
                        [
                            'name' => 'created_at',
                            'type' => 'int64',
                            'optional' => true,
                        ],
                        [
                            'name' => 'anime',
                            'type' => 'object[]',
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => implode(',', [
                        'name',
                        'anime.synonyms',
                    ]),
                    'query_by_weights' => '10,8',
                ],
            ],
            Song::class => [
                'collection-schema' => [
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                            'sort' => true,
                        ],
                        [
                            'name' => 'title',
                            'type' => 'string',
                            'optional' => true,
                        ],
                        [
                            'name' => 'title_native',
                            'type' => 'string',
                            'optional' => true,
                        ],
                        [
                            'name' => 'created_at',
                            'type' => 'int64',
                            'optional' => true,
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => 'title,title_native',
                    'query_by_weights' => '10,8',
                ],
            ],
            Studio::class => [
                'collection-schema' => [
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'name',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'created_at',
                            'type' => 'int64',
                            'optional' => true,
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => 'name',
                ],
            ],
            AnimeSynonym::class => [
                'collection-schema' => [
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'text',
                            'type' => 'string',
                            'sort' => true,
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => 'text',
                ],
            ],
            Synonym::class => [
                'collection-schema' => [
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'text',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'created_at',
                            'type' => 'int64',
                            'optional' => true,
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => 'text',
                ],
            ],
            Video::class => [
                'collection-schema' => [
                    'enable_nested_fields' => true,
                    'fields' => [
                        [
                            'name' => 'id',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'created_at',
                            'type' => 'int64',
                            'optional' => true,
                        ],
                        [
                            'name' => 'filename',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'tags',
                            'type' => 'string',
                        ],
                        [
                            'name' => 'entries',
                            'type' => 'object[]',
                        ],
                    ],
                ],
                'search-parameters' => [
                    'query_by' => implode(',', [
                        'filename',
                        'tags',
                        'entries.animetheme.song.title',
                        'entries.animetheme.song.title_native',
                        'entries.animetheme.anime.name',
                        'entries.animetheme.anime.synonyms',
                        'entries.type_sequence_version',
                    ]),
                    'query_by_weights' => '10,8,6,7,5,5,5',
                ],
            ],
        ],
    ],
];
