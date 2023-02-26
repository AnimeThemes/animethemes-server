<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\StudioImageSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Pivots\Wiki\StudioImage;
use Illuminate\Http\Request;

/**
 * Class StudioImageResource.
 */
class StudioImageResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'studioimage';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[StudioImage::RELATION_STUDIO] = new StudioResource($this->whenLoaded(StudioImage::RELATION_STUDIO), $this->query);
        $result[StudioImage::RELATION_IMAGE] = new ImageResource($this->whenLoaded(StudioImage::RELATION_IMAGE), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new StudioImageSchema();
    }
}
