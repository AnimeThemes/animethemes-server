<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use App\Models\Anime;
use Folklore\GraphQL\Support\Facades\GraphQL;
use Folklore\GraphQL\Support\Type as GraphQLType;

class AnimeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Anime',
        'description' => 'An Anime',
        'model' => Anime::class
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Anime id'
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Anime Name'
            ],
            'collection' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Year or decade'
            ],
            'season' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Year or decade'
            ],
            'mal_id' => [
                'type' => Type::int(),
                'description' => 'MyAnimeList ID'
            ],
            'anilist_id' => [
                'type' => Type::int(),
                'description' => 'Anilist.co ID'
            ],
            'kitsu_id' => [
                'type' => Type::int(),
                'description' => 'Kitsu ID'
            ],
            'anidb_id' => [
                'type' => Type::int(),
                'description' => 'Kitsu ID'
            ],
            'names' => [
                'type' => Type::listOf(GraphQL::type('AnimeName')),
                'description' => 'Anime Alternative Names'
            ],
            'themes' => [
                'type' => Type::listOf(GraphQL::type('Theme')),
                'description' => 'Anime Themes'
            ]
        ];
    }
}