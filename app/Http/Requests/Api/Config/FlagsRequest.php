<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Config;

use App\Http\Api\Query\Config\FlagsQuery;
use App\Http\Api\Query\Query;
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
    protected function getSchema(): Schema
    {
        return new FlagsSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return Query
     */
    public function getQuery(): Query
    {
        return new FlagsQuery($this->validated());
    }
}
