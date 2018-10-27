<?php


return [
    'prefix' => 'graphql',
    'domain' => null,
    'routes' => '{graphql_schema?}',
    'controllers' => \Folklore\GraphQL\GraphQLController::class.'@query',
    'variables_input_name' => 'variables',

    'middleware' => [],
    'middleware_schema' => [
        'default' => [],
    ],

    'headers' => [],
    'json_encoding_options' => 0,

    'graphiql' => [
        'routes' => '/graphiql/{graphql_schema?}',
        'controller' => \Folklore\GraphQL\GraphQLController::class.'@graphiql',
        'middleware' => [],
        'view' => 'graphql::graphiql',
        'composer' => \Folklore\GraphQL\View\GraphiQLComposer::class,
    ],

    'schema' => 'default',

    'schemas' => [
        'default' => [
            'query' => [
                'Anime' => 'App\GraphQL\Query\AnimeQuery',
                'Artist' => 'App\GraphQL\Query\ArtistQuery',
                'Serie' => 'App\GraphQL\Query\SerieQuery',
            ],
            'mutation' => [

            ]
        ]
    ],

    'resolvers' => [
        'default' => [
        ],
    ],

    'defaultFieldResolver' => null,

    'types' => [
        'Anime' => 'App\GraphQL\Type\AnimeType',
        'AnimeName' => 'App\GraphQL\Type\AnimeNameType',
        'Theme' => 'App\GraphQL\Type\ThemeType',
        'Video' => 'App\GraphQL\Type\VideoType',
        'Artist' => 'App\GraphQL\Type\ArtistType',
        'Serie' => 'App\GraphQL\Type\SerieType',
    ],

    'error_formatter' => [\Folklore\GraphQL\GraphQL::class, 'formatError'],

    'security' => [
        'query_max_complexity' => null,
        'query_max_depth' => null,
        'disable_introspection' => false
    ],

    'scalar_directory' => app_path("GraphQL/Types/Scalars"),
    'scalar_namespace' => 'App\GraphQL\Types\Scalars',
];
