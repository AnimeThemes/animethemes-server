<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Http\Api\Query;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AnimeIndexRequest.
 */
class AnimeIndexRequest extends IndexRequest
{
    /**
     * Get the underlying resource collection.
     *
     * @return BaseCollection
     */
    protected function getCollection(): BaseCollection
    {
        return AnimeCollection::make(new MissingValue(), Query::make());
    }
}
