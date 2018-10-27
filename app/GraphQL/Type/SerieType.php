<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use App\Models\Serie;
use Folklore\GraphQL\Support\Facades\GraphQL;
use Folklore\GraphQL\Support\Type as GraphQLType;

class SerieType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Serie',
        'description' => 'An Anime Serie Collection',
        'model' => Serie::class
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
            'animes' => [
                'type' => Type::listOf(GraphQL::type('Anime')),
                'description' => "Serie's Animes"
            ]
        ];
    }
}