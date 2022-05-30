<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Api\Query\ReadQuery;
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
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(mixed $resource, protected readonly ReadQuery $query)
    {
        parent::__construct($resource);
    }
}
