<?php

declare(strict_types=1);

use App\GraphQL\Definition\Mutations\Models\List\Playlist\CreatePlaylistMutation;
use App\GraphQL\Definition\Mutations\Models\List\Playlist\DeletePlaylistMutation;
use App\GraphQL\Definition\Mutations\Models\List\Playlist\Track\CreatePlaylistTrackMutation;
use App\GraphQL\Definition\Mutations\Models\List\Playlist\Track\DeletePlaylistTrackMutation;
use App\GraphQL\Definition\Mutations\Models\List\Playlist\Track\UpdatePlaylistTrackMutation;
use App\GraphQL\Definition\Mutations\Models\List\Playlist\UpdatePlaylistMutation;
use App\GraphQL\Definition\Queries\Admin\CurrentFeaturedThemeQuery;
use App\GraphQL\Definition\Queries\Auth\MeQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Admin\AnnouncementPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Admin\DumpPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Admin\FeaturedThemePaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Admin\FeaturePaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Document\PagePaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\List\ExternalProfilePaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\List\Playlist\PlaylistTrackPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\List\PlaylistPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\Anime\AnimeSynonymPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\Anime\AnimeThemePaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\Anime\Theme\AnimeThemeEntryPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\AnimePaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\ArtistPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\AudioPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\ExternalResourcePaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\ImagePaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\SeriesPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\Song\MembershipPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\Song\PerformancePaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\SongPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\StudioPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\ThemeGroupPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\Video\VideoScriptPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Pagination\Wiki\VideoPaginationQuery;
use App\GraphQL\Definition\Queries\Models\Singular\Document\PageQuery;
use App\GraphQL\Definition\Queries\Models\Singular\List\Playlist\PlaylistTrackQuery;
use App\GraphQL\Definition\Queries\Models\Singular\List\PlaylistQuery;
use App\GraphQL\Definition\Queries\Models\Singular\Wiki\AnimeQuery;
use App\GraphQL\Definition\Queries\Models\Singular\Wiki\ArtistQuery;
use App\GraphQL\Definition\Queries\Models\Singular\Wiki\SeriesQuery;
use App\GraphQL\Definition\Queries\Models\Singular\Wiki\StudioQuery;
use App\GraphQL\Definition\Queries\SearchQuery;
use App\GraphQL\Definition\Queries\Wiki\AnimeYearsQuery;
use App\GraphQL\Definition\Queries\Wiki\FindAnimeByExternalSiteQuery;
use App\GraphQL\Definition\Types\Admin\AnnouncementType;
use App\GraphQL\Definition\Types\Admin\DumpType;
use App\GraphQL\Definition\Types\Admin\FeaturedThemeType;
use App\GraphQL\Definition\Types\Admin\FeatureType;
use App\GraphQL\Definition\Types\Auth\PermissionType;
use App\GraphQL\Definition\Types\Auth\RoleType;
use App\GraphQL\Definition\Types\Auth\User\MeType;
use App\GraphQL\Definition\Types\Auth\UserType;
use App\GraphQL\Definition\Types\Base\PaginationInfoType;
use App\GraphQL\Definition\Types\Document\PageType;
use App\GraphQL\Definition\Types\List\External\ExternalEntryType;
use App\GraphQL\Definition\Types\List\ExternalProfileType;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\MessageResponseType;
use App\GraphQL\Definition\Types\SearchType;
use App\GraphQL\Definition\Types\User\Notification\ExternalProfileSyncedNotificationType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeSynonymType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYear\AnimeYearSeasonsType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYear\AnimeYearSeasonType;
use App\GraphQL\Definition\Types\Wiki\Anime\AnimeYearType;
use App\GraphQL\Definition\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\GraphQL\Definition\Types\Wiki\AudioType;
use App\GraphQL\Definition\Types\Wiki\ExternalResourceType;
use App\GraphQL\Definition\Types\Wiki\ImageType;
use App\GraphQL\Definition\Types\Wiki\SeriesType;
use App\GraphQL\Definition\Types\Wiki\Song\MembershipType;
use App\GraphQL\Definition\Types\Wiki\Song\PerformanceType;
use App\GraphQL\Definition\Types\Wiki\SongType;
use App\GraphQL\Definition\Types\Wiki\StudioType;
use App\GraphQL\Definition\Types\Wiki\ThemeGroupType;
use App\GraphQL\Definition\Types\Wiki\Video\VideoScriptType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\GraphQL\Definition\Unions\ImageableUnion;
use App\GraphQL\Definition\Unions\LikedUnion;
use App\GraphQL\Definition\Unions\NotificationUnion;
use App\GraphQL\Definition\Unions\PerformanceArtistUnion;
use App\GraphQL\Definition\Unions\ResourceableUnion;

return [
    'route' => [
        // The prefix for routes; do NOT use a leading slash!
        'prefix' => env('GRAPHQL_PATH', '/'),

        // The controller/method to use in GraphQL request.
        // Also supported array syntax: `[\Rebing\GraphQL\GraphQLController::class, 'query']`
        'controller' => Rebing\GraphQL\GraphQLController::class.'@query',

        // Any middleware for the graphql route group
        // This middleware will apply to all schemas
        'middleware' => [],

        // Additional route group attributes
        //
        // Example:
        //
        // 'group_attributes' => ['guard' => 'api']
        //
        'group_attributes' => [
            'guard' => 'web',
        ],
    ],

    // The name of the default schema
    // Used when the route group is directly accessed
    'default_schema' => 'default',

    'batching' => [
        // Whether to support GraphQL batching or not.
        // See e.g. https://www.apollographql.com/blog/batching-client-graphql-queries-a685f5bcd41b/
        // for pro and con
        'enable' => true,
    ],

    // The schemas for query and/or mutation. It expects an array of schemas to provide
    // both the 'query' fields and the 'mutation' fields.
    //
    // You can also provide a middleware that will only apply to the given schema
    //
    // Example:
    //
    //  'schemas' => [
    //      'default' => [
    //          'controller' => MyController::class . '@method',
    //          'query' => [
    //              App\GraphQL\Queries\UsersQuery::class,
    //          ],
    //          'mutation' => [
    //
    //          ]
    //      ],
    //      'user/me' => [
    //          'query' => [
    //              App\GraphQL\Queries\MyProfileQuery::class,
    //          ],
    //          'mutation' => [
    //
    //          ],
    //          'middleware' => ['auth'],
    //      ],
    //  ]
    //
    'schemas' => [
        'default' => [
            'query' => [
                // Admin
                AnnouncementPaginationQuery::class,
                DumpPaginationQuery::class,
                FeaturePaginationQuery::class,
                FeaturedThemePaginationQuery::class,
                CurrentFeaturedThemeQuery::class,

                // Auth
                MeQuery::class,

                // Document
                PageQuery::class,
                PagePaginationQuery::class,

                // List
                ExternalProfilePaginationQuery::class,
                PlaylistQuery::class,
                PlaylistTrackQuery::class,
                PlaylistPaginationQuery::class,
                PlaylistTrackPaginationQuery::class,

                // Wiki
                AnimeQuery::class,
                AnimePaginationQuery::class,
                AnimeSynonymPaginationQuery::class,
                AnimeThemePaginationQuery::class,
                AnimeThemeEntryPaginationQuery::class,
                ArtistQuery::class,
                ArtistPaginationQuery::class,
                AudioPaginationQuery::class,
                ExternalResourcePaginationQuery::class,
                ImagePaginationQuery::class,
                MembershipPaginationQuery::class,
                PerformancePaginationQuery::class,
                SeriesQuery::class,
                SeriesPaginationQuery::class,
                SongPaginationQuery::class,
                StudioQuery::class,
                StudioPaginationQuery::class,
                ThemeGroupPaginationQuery::class,
                VideoPaginationQuery::class,
                VideoScriptPaginationQuery::class,

                // Others
                AnimeYearsQuery::class,
                FindAnimeByExternalSiteQuery::class,
                SearchQuery::class,
            ],
            'mutation' => [
                CreatePlaylistMutation::class,
                UpdatePlaylistMutation::class,
                DeletePlaylistMutation::class,

                CreatePlaylistTrackMutation::class,
                UpdatePlaylistTrackMutation::class,
                DeletePlaylistTrackMutation::class,
            ],
            'types' => [
                // Admin
                AnnouncementType::class,
                DumpType::class,
                FeatureType::class,
                FeaturedThemeType::class,

                // Auth
                MeType::class,
                RoleType::class,
                PermissionType::class,
                UserType::class,

                // Document
                PageType::class,

                // List
                ExternalProfileType::class,
                ExternalEntryType::class,
                PlaylistType::class,
                PlaylistTrackType::class,

                // User
                ExternalProfileSyncedNotificationType::class,

                // Wiki
                AnimeType::class,
                AnimeSynonymType::class,
                AnimeThemeType::class,
                AnimeThemeEntryType::class,
                ArtistType::class,
                AudioType::class,
                ExternalResourceType::class,
                ImageType::class,
                MembershipType::class,
                PerformanceType::class,
                SeriesType::class,
                SongType::class,
                StudioType::class,
                ThemeGroupType::class,
                VideoType::class,
                VideoScriptType::class,

                // Others
                AnimeYearSeasonsType::class,
                AnimeYearSeasonType::class,
                AnimeYearType::class,
                MessageResponseType::class,
                SearchType::class,

                // Pivot
                // ResourceableType::class,

                // Unions

                ImageableUnion::class,
                LikedUnion::class,
                NotificationUnion::class,
                PerformanceArtistUnion::class,
                ResourceableUnion::class,
            ],

            // Laravel HTTP middleware
            'middleware' => [
                // Set the serving context to graphql.
                App\Http\Middleware\GraphQL\SetServingGraphQL::class,

                // Rate limiting GraphQL to prevent abuse.
                'throttle:graphql',

                // Allow client to get full database.
                App\Http\Middleware\GraphQL\MaxCount::class,

                // Logs GraphQL Requests.
                App\Http\Middleware\GraphQL\LogGraphQLRequest::class,
            ],

            // Which HTTP methods to support; must be given in UPPERCASE!
            'method' => ['POST'],

            // An array of middlewares, overrides the global ones
            'execution_middleware' => null,
        ],
    ],

    // The global types available to all schemas.
    // You can then access it from the facade like this: GraphQL::type('user')
    'types' => [
        // Pagination
        PaginationInfoType::class,
        // ExampleType::class,
        // ExampleRelationType::class,
        // \Rebing\GraphQL\Support\UploadType::class,
    ],

    // This callable will be passed the Error object for each errors GraphQL catch.
    // The method should return an array representing the error.
    // Typically:
    // [
    //     'message' => '',
    //     'locations' => []
    // ]
    'error_formatter' => [Rebing\GraphQL\GraphQL::class, 'formatError'],

    /*
     * Custom Error Handling
     *
     * Expected handler signature is: function (array $errors, callable $formatter): array
     *
     * The default handler will pass exceptions to laravel Error Handling mechanism
     */
    'errors_handler' => [Rebing\GraphQL\GraphQL::class, 'handleErrors'],

    /*
     * Options to limit the query complexity and depth. See the doc
     * @ https://webonyx.github.io/graphql-php/security
     * for details. Disabled by default.
     */
    'security' => [
        'query_max_complexity' => 217,
        'query_max_depth' => 13,
        'disable_introspection' => false,
    ],

    // Custom array
    'pagination_values' => [
        'default_count' => 15,
        'max_count' => 10,
        'relation' => [
            'default_count' => 1000000,
            'max_count' => null,
        ],
    ],

    /*
     * You can define your own pagination type.
     * Reference \Rebing\GraphQL\Support\PaginationType::class
     */
    'pagination_type' => App\GraphQL\Definition\Types\Base\PaginationType::class,

    /*
     * You can define your own simple pagination type.
     * Reference \Rebing\GraphQL\Support\SimplePaginationType::class
     */
    'simple_pagination_type' => Rebing\GraphQL\Support\SimplePaginationType::class,

    /*
     * You can define your own cursor pagination type.
     * Reference Rebing\GraphQL\Support\CursorPaginationType::class
     */
    'cursor_pagination_type' => Rebing\GraphQL\Support\CursorPaginationType::class,

    /*
     * Overrides the default field resolver
     * See http://webonyx.github.io/graphql-php/data-fetching/#default-field-resolver
     *
     * Example:
     *
     * ```php
     * 'defaultFieldResolver' => function ($root, $args, $context, $info) {
     * },
     * ```
     * or
     * ```php
     * 'defaultFieldResolver' => [SomeKlass::class, 'someMethod'],
     * ```
     */
    'defaultFieldResolver' => null,

    /*
     * Any headers that will be added to the response returned by the default controller
     */
    'headers' => [],

    /*
     * Any JSON encoding options when returning a response from the default controller
     * See http://php.net/manual/function.json-encode.php for the full list of options
     */
    'json_encoding_options' => 0,

    /*
     * Automatic Persisted Queries (APQ)
     * See https://www.apollographql.com/docs/apollo-server/performance/apq/
     *
     * Note 1: this requires the `AutomaticPersistedQueriesMiddleware` being enabled
     *
     * Note 2: even if APQ is disabled per configuration and, according to the "APQ specs" (see above),
     *         to return a correct response in case it's not enabled, the middleware needs to be active.
     *         Of course if you know you do not have a need for APQ, feel free to remove the middleware completely.
     */
    'apq' => [
        // Enable/Disable APQ - See https://www.apollographql.com/docs/apollo-server/performance/apq/#disabling-apq
        'enable' => env('GRAPHQL_APQ_ENABLE', false),

        // The cache driver used for APQ
        'cache_driver' => env('GRAPHQL_APQ_CACHE_DRIVER', config('cache.default')),

        // The cache prefix
        'cache_prefix' => config('cache.prefix').':graphql.apq',

        // The cache ttl in seconds - See https://www.apollographql.com/docs/apollo-server/performance/apq/#adjusting-cache-time-to-live-ttl
        'cache_ttl' => 300,
    ],

    /*
     * Execution middlewares
     */
    'execution_middleware' => [
        Rebing\GraphQL\Support\ExecutionMiddleware\ValidateOperationParamsMiddleware::class,
        // AutomaticPersistedQueriesMiddleware listed even if APQ is disabled, see the docs for the `'apq'` configuration
        Rebing\GraphQL\Support\ExecutionMiddleware\AutomaticPersistedQueriesMiddleware::class,
        Rebing\GraphQL\Support\ExecutionMiddleware\AddAuthUserContextValueMiddleware::class,
        // \Rebing\GraphQL\Support\ExecutionMiddleware\UnusedVariablesMiddleware::class,
    ],

    /*
     * Globally registered ResolverMiddleware
     */
    'resolver_middleware_append' => null,
];
