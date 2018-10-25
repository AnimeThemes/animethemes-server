<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use App\Models\AnimeName;
use Folklore\GraphQL\Support\Type as GraphQLType;

class AnimeNameType extends GraphQLType
{
    protected $attributes = [
        'name' => 'AnimeName',
        'description' => 'An Anime Alternative Name',
        'model' => AnimeName::class
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Entry Id'
            ],
            'anime_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Anime id'
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Title itself'
            ],
            'language' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Language'
            ]
        ];
    }
}