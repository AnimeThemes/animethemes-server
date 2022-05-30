<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Query\Wiki\Anime\AnimeReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Requests\Api\ShowRequest;

/**
 * Class YearShowRequest.
 */
class YearShowRequest extends ShowRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new AnimeSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return ReadQuery
     */
    public function getQuery(): ReadQuery
    {
        return new AnimeReadQuery($this->validated());
    }
}
