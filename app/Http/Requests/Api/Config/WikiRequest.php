<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Config;

use App\Http\Api\Query\Config\WikiQuery;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Config\WikiSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Requests\Api\ShowRequest;

/**
 * Class WikiRequest.
 */
class WikiRequest extends ShowRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
    {
        return new WikiSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return Query
     */
    public function getQuery(): Query
    {
        return new WikiQuery($this->validated());
    }
}
