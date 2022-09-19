<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin\Dump;

use App\Http\Api\Query\Admin\Dump\DumpWriteQuery;
use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Schema\Admin\DumpSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\Base\EloquentForceDeleteRequest;

/**
 * Class DumpForceDeleteRequest.
 */
class DumpForceDeleteRequest extends EloquentForceDeleteRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new DumpSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new DumpWriteQuery($this->validated());
    }
}
