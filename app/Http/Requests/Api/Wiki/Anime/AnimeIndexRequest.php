<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\AnimeQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;

/**
 * Class AnimeIndexRequest.
 */
class AnimeIndexRequest extends EloquentIndexRequest implements SearchableRequest
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
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return new AnimeQuery($this->validated());
    }
}
