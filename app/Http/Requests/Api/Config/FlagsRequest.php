<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Config;

use App\Http\Api\Query\Config\FlagsReadQuery;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Config\FlagsSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Requests\Api\ShowRequest;

/**
 * Class FlagsRequest.
 */
class FlagsRequest extends ShowRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new FlagsSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return ReadQuery
     */
    public function getQuery(): ReadQuery
    {
        return new FlagsReadQuery($this->validated());
    }
}
