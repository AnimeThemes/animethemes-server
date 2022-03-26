<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Video;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Video\VideoWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Requests\Api\Base\EloquentStoreRequest;

/**
 * Class VideoStoreRequest.
 */
class VideoStoreRequest extends EloquentStoreRequest
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

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    protected function tokenAbility(): string
    {
        return 'video:create';
    }
}
