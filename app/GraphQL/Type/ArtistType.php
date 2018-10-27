<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use App\Models\Artist;
use Folklore\GraphQL\Support\Facades\GraphQL;
use Folklore\GraphQL\Support\Type as GraphQLType;

class ArtistType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Artist',
        'description' => 'An Artist Entry',
        'model' => Artist::class
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
            'themes' => [
                'type' => Type::listOf(GraphQL::type('Theme')),
                'description' => "Artist's Themes"
            ]
        ];
    }
}