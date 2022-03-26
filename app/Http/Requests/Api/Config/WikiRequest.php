<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Config;

use App\Http\Api\Query\Config\WikiReadQuery;
use App\Http\Api\Query\ReadQuery;
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
    protected function schema(): Schema
    {
        return new WikiSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return ReadQuery
     */
    public function getQuery(): ReadQuery
    {
        return new WikiReadQuery($this->validated());
    }
}
