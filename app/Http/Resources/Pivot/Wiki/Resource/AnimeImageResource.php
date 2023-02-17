<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\AnimeImageSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Pivots\Wiki\AnimeImage;
use Illuminate\Http\Request;

/**
 * Class AnimeImageResource.
 */
class AnimeImageResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animeimage';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[AnimeImage::RELATION_ANIME] = new AnimeResource($this->whenLoaded(AnimeImage::RELATION_ANIME), $this->query);
        $result[AnimeImage::RELATION_IMAGE] = new ImageResource($this->whenLoaded(AnimeImage::RELATION_IMAGE), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new AnimeImageSchema();
    }
}
