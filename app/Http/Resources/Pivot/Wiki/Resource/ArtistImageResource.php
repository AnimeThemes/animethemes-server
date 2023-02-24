<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\ArtistImageSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Pivots\Wiki\ArtistImage;
use Illuminate\Http\Request;

/**
 * Class ArtistImageResource.
 */
class ArtistImageResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artistimage';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[ArtistImage::RELATION_ARTIST] = new ArtistResource($this->whenLoaded(ArtistImage::RELATION_ARTIST), $this->query);
        $result[ArtistImage::RELATION_IMAGE] = new ImageResource($this->whenLoaded(ArtistImage::RELATION_IMAGE), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ArtistImageSchema();
    }
}
