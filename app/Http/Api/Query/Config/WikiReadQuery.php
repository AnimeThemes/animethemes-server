<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Config;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Config\WikiSchema;
use App\Http\Api\Schema\Schema;

/**
 * Class WikiReadQuery.
 */
class WikiReadQuery extends ReadQuery
{
    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new WikiSchema();
    }
}
