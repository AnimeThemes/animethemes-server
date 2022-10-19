<?php

declare(strict_types=1);

namespace App\Http\Api\Query;

/**
 * Class WriteQuery.
 */
abstract class WriteQuery
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
