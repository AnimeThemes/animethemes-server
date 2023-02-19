<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\AnimeStudioSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Http\Request;

/**
 * Class AnimeStudioResource.
 */
class AnimeStudioResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animestudio';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[AnimeStudio::RELATION_ANIME] = new AnimeResource($this->whenLoaded(AnimeStudio::RELATION_ANIME), $this->query);
        $result[AnimeStudio::RELATION_STUDIO] = new StudioResource($this->whenLoaded(AnimeStudio::RELATION_STUDIO), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new AnimeStudioSchema();
    }
}
