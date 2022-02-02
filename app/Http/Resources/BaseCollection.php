<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Api\Query\Query;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class BaseCollection.
 */
abstract class BaseCollection extends ResourceCollection
{
    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  Query  $query
     * @return void
     */
    public function __construct(mixed $resource, protected Query $query)
    {
        parent::__construct($resource);
    }
}
