<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Document\Page;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\Document\PageSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Document\Collection\PageCollection;
use App\Http\Resources\Document\Resource\PageResource;
use App\Models\Document\Page;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class PageReadQuery.
 */
class PageReadQuery extends EloquentReadQuery
{
    /**
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new PageSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function indexBuilder(): Builder
    {
        return Page::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new PageResource($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return new PageCollection($resource, $this);
    }
}
