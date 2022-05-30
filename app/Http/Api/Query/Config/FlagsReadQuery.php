<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Config;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Config\FlagsSchema;
use App\Http\Api\Schema\Schema;

/**
 * Class FlagsReadQuery.
 */
class FlagsReadQuery extends ReadQuery
{
    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new FlagsSchema();
    }
}
