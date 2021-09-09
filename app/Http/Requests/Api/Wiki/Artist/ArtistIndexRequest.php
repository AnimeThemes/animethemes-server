<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Artist;

use App\Http\Api\Query;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ArtistIndexRequest.
 */
class ArtistIndexRequest extends IndexRequest
{
    /**
     * Get the underlying resource collection.
     *
     * @return BaseCollection
     */
    protected function getCollection(): BaseCollection
    {
        return ArtistCollection::make(new MissingValue(), Query::make());
    }
}
