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
use App\GraphQL\Definition\Queries\Models\Paginator\Admin\AnnouncementPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Admin\DumpPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Admin\FeaturedThemePaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Admin\FeaturePaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Document\PagePaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\List\ExternalProfilePaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\List\Playlist\PlaylistTrackPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\List\PlaylistPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\Anime\AnimeSynonymPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\Anime\AnimeThemePaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\Anime\Theme\AnimeThemeEntryPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\AnimePaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\ArtistPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\AudioPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\ExternalResourcePaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\ImagePaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\SeriesPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\Song\MembershipPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\Song\PerformancePaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\SongPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\StudioPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\ThemeGroupPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\Video\VideoScriptPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Paginator\Wiki\VideoPaginatorQuery;
use App\GraphQL\Definition\Queries\Models\Singular\Document\PageQuery;
use App\GraphQL\Definition\Queries\Models\Singular\List\Playlist\PlaylistTrackQuery;
use App\GraphQL\Definition\Queries\Models\Singular\List\PlaylistQuery;
use App\GraphQL\Definition\Queries\Models\Singular\Wiki\AnimeQuery;
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
use App\GraphQL\Definition\Types\Base\PaginatorInfoType;
use App\GraphQL\Definition\Types\Document\PageType;
use App\GraphQL\Definition\Types\List\External\ExternalEntryType;
use App\GraphQL\Definition\Types\List\ExternalProfileType;
use App\GraphQL\Definition\Types\List\Playlist\PlaylistTrackType;
use App\GraphQL\Definition\Types\List\PlaylistType;
use App\GraphQL\Definition\Types\MessageResponseType;
use App\GraphQL\Definition\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Definition\Types\SearchType;
use App\GraphQL\Definition\Types\User\Notification\NotificationDataType;
use App\GraphQL\Definition\Types\User\NotificationType;
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
use App\GraphQL\Definition\Unions\LikedUnion;
use App\GraphQL\Definition\Unions\PerformanceArtistUnion;
use App\GraphQL\Definition\Unions\ResourceableUnion;

return [
    'route' => [
        // The prefix for routes; do NOT use a leading slash!
        'prefix' => 'graphql',

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
        'group_attributes' => [],
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
                AnnouncementPaginatorQuery::class,
                DumpPaginatorQuery::class,
                FeaturePaginatorQuery::class,
                FeaturedThemePaginatorQuery::class,
                CurrentFeaturedThemeQuery::class,

                // Auth
                MeQuery::class,

                // Document
                PageQuery::class,
                PagePaginatorQuery::class,

                // List
                ExternalProfilePaginatorQuery::class,
                PlaylistQuery::class,
                PlaylistTrackQuery::class,
                PlaylistPaginatorQuery::class,
                PlaylistTrackPaginatorQuery::class,

                // Wiki
                AnimeQuery::class,
                AnimePaginatorQuery::class,
                AnimeSynonymPaginatorQuery::class,
                AnimeThemePaginatorQuery::class,
                AnimeThemeEntryPaginatorQuery::class,
                ArtistPaginatorQuery::class,
                AudioPaginatorQuery::class,
                ExternalResourcePaginatorQuery::class,
                ImagePaginatorQuery::class,
                MembershipPaginatorQuery::class,
                PerformancePaginatorQuery::class,
                SeriesPaginatorQuery::class,
                SongPaginatorQuery::class,
                StudioPaginatorQuery::class,
                ThemeGroupPaginatorQuery::class,
                VideoPaginatorQuery::class,
                VideoScriptPaginatorQuery::class,

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
                NotificationType::class,
                NotificationDataType::class,

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
                LikedUnion::class,
                PerformanceArtistUnion::class,
                ResourceableUnion::class,
            ],

            // Laravel HTTP middleware
            'middleware' => [
                // Set the serving context to graphql.
                App\Http\Middleware\GraphQL\SetServingGraphQL::class,

                // Rate limiting GraphQL to prevent abuse.
                'throttle:graphql',

                // GraphQL needs to have their own policies.
                App\Http\Middleware\GraphQL\GraphQLPolicy::class,

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
        // Paginator
        PaginatorInfoType::class,
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
    'pagination_type' => App\GraphQL\Definition\Types\Base\PaginatorType::class,

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
