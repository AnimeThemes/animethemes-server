<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pivot\Wiki\AnimeImage;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Pivot\Wiki\AnimeImage\AnimeImageReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Pivot\Wiki\AnimeImageSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;

/**
 * Class AnimeImageIndexRequest.
 */
class AnimeImageIndexRequest extends EloquentIndexRequest
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
