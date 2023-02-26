<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\ArtistResourceSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Pivots\Wiki\ArtistResource as ArtistResourcePivot;
use Illuminate\Http\Request;

/**
 * Class ArtistResourceResource.
 */
class ArtistResourceResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artistresource';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[ArtistResourcePivot::RELATION_ARTIST] = new ArtistResource($this->whenLoaded(ArtistResourcePivot::RELATION_ARTIST), $this->query);
        $result[ArtistResourcePivot::RELATION_RESOURCE] = new ExternalResourceResource($this->whenLoaded(ArtistResourcePivot::RELATION_RESOURCE), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ArtistResourceSchema();
    }
}
