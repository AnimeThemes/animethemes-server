<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\StudioResourceSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Pivots\Wiki\StudioResource as StudioResourcePivot;
use Illuminate\Http\Request;

/**
 * Class StudioResourceResource.
 */
class StudioResourceResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'studioresource';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[StudioResourcePivot::RELATION_STUDIO] = new StudioResource($this->whenLoaded(StudioResourcePivot::RELATION_STUDIO), $this->query);
        $result[StudioResourcePivot::RELATION_RESOURCE] = new ExternalResourceResource($this->whenLoaded(StudioResourcePivot::RELATION_RESOURCE), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new StudioResourceSchema();
    }
}
