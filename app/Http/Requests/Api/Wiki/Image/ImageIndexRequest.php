<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Image;

use App\Http\Api\Parser\SearchParser;
use App\Http\Api\Query;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ImageIndexRequest.
 */
class ImageIndexRequest extends IndexRequest
{
    /**
     * Get the underlying resource collection.
     *
     * @return BaseCollection
     */
    protected function getCollection(): BaseCollection
    {
        return ImageCollection::make(new MissingValue(), Query::make());
    }

    /**
     * Get the search validation rules.
     *
     * @return array
     */
    protected function getSearchRules(): array
    {
        return [
            SearchParser::$param => [
                'prohibited',
            ],
        ];
    }
}
