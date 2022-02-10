<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Http\Api\Query\Query;
use App\Http\Api\Query\Wiki\AnimeQuery;
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
    protected function getSchema(): EloquentSchema
    {
        return new AnimeSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return Query
     */
    public function getQuery(): Query
    {
        return AnimeQuery::make($this->validated());
    }
}
