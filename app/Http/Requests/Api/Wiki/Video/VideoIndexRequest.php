<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Video;

use App\Contracts\Http\Requests\Api\SearchableRequest;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\VideoQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Requests\Api\EloquentIndexRequest;

/**
 * Class VideoIndexRequest.
 */
class VideoIndexRequest extends EloquentIndexRequest implements SearchableRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
    {
        return new VideoSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return VideoQuery::make($this->validated());
    }
}
