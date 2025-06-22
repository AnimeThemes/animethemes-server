<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Wiki\Video;

use App\GraphQL\Definition\Queries\EloquentQuery;
use App\GraphQL\Definition\Types\Wiki\Video\VideoScriptType;

/**
 * Class VideoScriptsQuery.
 */
class VideoScriptsQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct('scripts');
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Returns a listing of scripts resources given fields.';
    }

    /**
     * The arguments of the type.
     *
     * @return array<int, string>
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            'orderBy: _ @orderBy(columnsEnum: "VideoScriptColumnsOrderable")',
        ];
    }

    /**
     * The base return type of the query.
     *
     * @return VideoScriptType
     */
    public function baseType(): VideoScriptType
    {
        return new VideoScriptType();
    }
}
