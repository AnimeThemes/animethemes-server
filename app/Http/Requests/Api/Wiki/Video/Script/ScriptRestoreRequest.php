<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Video\Script;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Video\Script\ScriptWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Requests\Api\Base\EloquentRestoreRequest;

/**
 * Class ScriptRestoreRequest.
 */
class ScriptRestoreRequest extends EloquentRestoreRequest
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
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new ScriptWriteQuery($this->validated());
    }
}
