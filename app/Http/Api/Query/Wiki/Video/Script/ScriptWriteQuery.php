<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Wiki\Video\Script;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Video\Resource\ScriptResource;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ScriptWriteQuery.
 */
class ScriptWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new ScriptSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return VideoScript::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new ScriptResource($resource, new ScriptReadQuery());
    }
}
