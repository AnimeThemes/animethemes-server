<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Request;

/**
 * Class ExternalResourceCollection.
 */
class ExternalResourceCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'resources';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = ExternalResource::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(
            fn (ExternalResource $resource) => ExternalResourceResource::make($resource, $this->query)
        )->all();
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new ExternalResourceSchema();
    }
}
