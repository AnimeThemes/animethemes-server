<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Video;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Video\VideoWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Requests\Api\Base\EloquentDestroyRequest;

/**
 * Class VideoDestroyRequest.
 */
class VideoDestroyRequest extends EloquentDestroyRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new VideoSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new VideoWriteQuery($this->validated());
    }
}
