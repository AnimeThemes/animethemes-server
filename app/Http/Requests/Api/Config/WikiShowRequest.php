<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Config;

use App\Http\Api\Schema\Config\WikiSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Requests\Api\ShowRequest;

/**
 * Class WikiShowRequest.
 */
class WikiShowRequest extends ShowRequest
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
}
