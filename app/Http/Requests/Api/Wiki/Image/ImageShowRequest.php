<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Image;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Query\Wiki\ImageQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Requests\Api\EloquentShowRequest;

/**
 * Class ImageShowRequest.
 */
class ImageShowRequest extends EloquentShowRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
    {
        return new ImageSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return ImageQuery::make($this->validated());
    }
}
