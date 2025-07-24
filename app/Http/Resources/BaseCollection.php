<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Api\Query\Query;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseCollection extends ResourceCollection
{
    public function __construct(mixed $resource, protected readonly Query $query)
    {
        parent::__construct($resource);
    }
}
