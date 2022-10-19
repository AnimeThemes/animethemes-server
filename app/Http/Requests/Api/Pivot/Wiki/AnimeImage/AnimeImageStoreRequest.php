<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pivot\Wiki\AnimeImage;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Pivot\Wiki\AnimeImage\AnimeImageWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\Wiki\AnimeImageSchema;
use App\Http\Requests\Api\Base\EloquentStoreRequest;

/**
 * Class AnimeImageStoreRequest.
 */
class AnimeImageStoreRequest extends EloquentStoreRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new AnimeImageSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new AnimeImageWriteQuery($this->validated());
    }
}
