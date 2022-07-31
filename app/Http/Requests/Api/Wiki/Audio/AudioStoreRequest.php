<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Audio;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Audio\AudioWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Requests\Api\Base\EloquentStoreRequest;

/**
 * Class AudioStoreRequest.
 */
class AudioStoreRequest extends EloquentStoreRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new AudioSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new AudioWriteQuery($this->validated());
    }
}
