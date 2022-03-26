<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Image;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Wiki\Image\ImageReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

/**
 * Class ImageShowRequest.
 */
class ImageShowRequest extends EloquentShowRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new ImageSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new ImageReadQuery($this->validated());
    }
}
