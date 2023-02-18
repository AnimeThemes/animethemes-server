<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\AnimeResourceSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Pivots\Wiki\AnimeResource as AnimeResourcePivot;
use Illuminate\Http\Request;

/**
 * Class AnimeResourceResource.
 */
class AnimeResourceResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animeresource';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[AnimeResourcePivot::RELATION_ANIME] = new AnimeResource($this->whenLoaded(AnimeResourcePivot::RELATION_ANIME), $this->query);
        $result[AnimeResourcePivot::RELATION_RESOURCE] = new ExternalResourceResource($this->whenLoaded(AnimeResourcePivot::RELATION_RESOURCE), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new AnimeResourceSchema();
    }
}
