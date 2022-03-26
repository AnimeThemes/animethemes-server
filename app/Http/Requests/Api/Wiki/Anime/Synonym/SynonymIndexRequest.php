<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Synonym;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Wiki\Anime\Synonym\SynonymReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\SynonymSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;

/**
 * Class SynonymIndexRequest.
 */
class SynonymIndexRequest extends EloquentIndexRequest implements SearchableRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new SynonymSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new SynonymReadQuery($this->validated());
    }
}
