<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pivot\AnimeImage;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Pivot\AnimeImage\AnimeImageReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\AnimeImageSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

/**
 * Class AnimeImageShowRequest.
 */
class AnimeImageShowRequest extends EloquentShowRequest
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
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new AnimeImageReadQuery($this->validated());
    }
}
