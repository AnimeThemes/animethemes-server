<?php

declare(strict_types=1);

namespace App\Http\Api\Query;

use App\Contracts\Http\Api\Query\QueryInterface;

/**
 * Class WriteQuery.
 */
abstract class WriteQuery implements QueryInterface
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
