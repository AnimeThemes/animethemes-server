<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use App\Models\Video;
use Folklore\GraphQL\Support\Type as GraphQLType;

class VideoType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Video',
        'description' => 'An Theme Video',
        'model' => Video::class
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Entry Id'
            ],
            'theme_id' => [
                'type' => Type::int(),
                'description' => 'Anime id'
            ],
            'basename' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Basename'
            ],
            'filename' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'File name'
            ],
            'path' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Video Path in S3'
            ],
            'quality' => [
                'type' => Type::int(),
                'description' => 'Quality'
            ],
            'isNC' => [
                'type' => Type::boolean(),
                'description' => 'If its not have any credits'
            ],
            'isLyrics' => [
                'type' => Type::boolean(),
                'description' => 'If its contains Lyrics'
            ],
            'isSubbed' => [
                'type' => Type::boolean(),
                'description' => 'If its have subtitles'
            ],
            'isUncensored' => [
                'type' => Type::boolean(),
                'description' => 'If its uncensored'
            ],
            'isOver' => [
                'type' => Type::boolean(),
                'description' => 'If song plays over the episode'
            ],
            'isTrans' => [
                'type' => Type::boolean(),
                'description' => 'If the video contains part of the episode which transitions into the OP or ED'
            ],
            'source' => [
                'type' => Type::string(),
                'description' => 'Theme Source DVD/BD/TV/VHS/VN/Game'
            ]
        ];
    }
}