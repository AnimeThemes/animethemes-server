<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin\Dump;

use App\Http\Api\Query\Admin\Dump\DumpReadQuery;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\Admin\DumpSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\Base\EloquentIndexRequest;

/**
 * Class DumpIndexRequest.
 */
class DumpIndexRequest extends EloquentIndexRequest
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
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new DumpReadQuery($this->validated());
    }
}
