<?php

declare(strict_types=1);

namespace App\Http\Api\Query;

use App\Contracts\Http\Api\Query\Query;

/**
 * Class WriteQuery.
 */
abstract class WriteQuery implements Query
{
    /**
     * Create a new query instance.
     *
     * @param  array  $parameters
     */
    public function __construct(protected readonly array $parameters = [])
    {
    }
}
