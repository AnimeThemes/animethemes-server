<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\VideoType;

/**
 * Class VideosQuery.
 */
class VideosQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('videos');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of video resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return string[]
     */
    public function arguments(): array
    {
        return [
            'search: String @search',

            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "VideoColumnsOrderable",
                relations: [
                    {relation: "likeAggregate", columns: ["value"]},
                    {relation: "viewAggregate", columns: ["value"]}
                ]
            )',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return VideoType
     */
    public function baseType(): VideoType
    {
        return new VideoType();
    }
}
