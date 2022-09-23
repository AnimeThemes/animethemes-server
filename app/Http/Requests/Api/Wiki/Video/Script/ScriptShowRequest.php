<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Video\Script;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Wiki\Video\Script\ScriptReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

/**
 * Class ScriptShowRequest.
 */
class ScriptShowRequest extends EloquentShowRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new ScriptSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new ScriptReadQuery($this->validated());
    }
}
