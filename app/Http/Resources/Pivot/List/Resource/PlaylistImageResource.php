<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\List\Resource;

use App\Http\Api\Schema\Pivot\List\PlaylistImageSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\List\Resource\PlaylistResource;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Pivots\List\PlaylistImage;
use Illuminate\Http\Request;

/**
 * Class PlaylistImageResource.
 */
class PlaylistImageResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'playlistimage';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[PlaylistImage::RELATION_PLAYLIST] = new PlaylistResource($this->whenLoaded(PlaylistImage::RELATION_PLAYLIST), $this->query);
        $result[PlaylistImage::RELATION_IMAGE] = new ImageResource($this->whenLoaded(PlaylistImage::RELATION_IMAGE), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new PlaylistImageSchema();
    }
}
