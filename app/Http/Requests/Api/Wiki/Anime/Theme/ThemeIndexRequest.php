<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime\Theme;

use App\Http\Api\Query;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ThemeIndexRequest.
 */
class ThemeIndexRequest extends IndexRequest
{
    /**
     * Get the underlying resource collection.
     *
     * @return BaseCollection
     */
    protected function getCollection(): BaseCollection
    {
        return ThemeCollection::make(new MissingValue(), Query::make());
    }
}
