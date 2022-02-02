<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Video;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\VideoQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Requests\Api\EloquentShowRequest;

/**
 * Class VideoShowRequest.
 */
class VideoShowRequest extends EloquentShowRequest
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
