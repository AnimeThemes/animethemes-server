<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\AudioType;

/**
 * Class AudiosQuery.
 */
class AudiosQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('audios');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of audio resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return string[]
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "AudioColumnsOrderable",
                relations: [
                    {relation: "viewAggregate", columns: ["value"]}
                ]
            )',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return AudioType
     */
    public function baseType(): AudioType
    {
        return new AudioType();
    }
}
