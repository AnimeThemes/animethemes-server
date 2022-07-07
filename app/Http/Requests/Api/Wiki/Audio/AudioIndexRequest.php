<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Audio;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Wiki\Audio\AudioReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;

/**
 * Class AudioIndexRequest.
 */
class AudioIndexRequest extends EloquentIndexRequest
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
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new AudioReadQuery($this->validated());
    }
}
