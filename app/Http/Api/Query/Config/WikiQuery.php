<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Config;

use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Config\WikiSchema;
use App\Http\Api\Schema\Schema;

/**
 * Class WikiQuery.
 */
class WikiQuery extends Query
{
    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new WikiSchema();
    }
}
