<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Wiki\Anime\AnimeReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

/**
 * Class AnimeShowRequest.
 */
class AnimeShowRequest extends EloquentShowRequest
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
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new AnimeReadQuery($this->validated());
    }
}
