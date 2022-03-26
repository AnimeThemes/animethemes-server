<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Studio;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Wiki\Studio\StudioWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Requests\Api\Base\EloquentRestoreRequest;

/**
 * Class StudioRestoreRequest.
 */
class StudioRestoreRequest extends EloquentRestoreRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new StudioSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new StudioWriteQuery($this->validated());
    }

    /**
     * The token ability to authorize.
     *
     * @return string
     */
    protected function tokenAbility(): string
    {
        return 'studio:restore';
    }
}
