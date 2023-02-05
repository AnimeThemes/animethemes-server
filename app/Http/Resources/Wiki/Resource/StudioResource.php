<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource as StudioResourcePivot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class StudioResource.
 *
 * @mixin Studio
 */
class StudioResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'studio';

    /**
     * Create a new resource instance.
     *
     * @param  Studio | MissingValue | null  $studio
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Studio|MissingValue|null $studio, ReadQuery $query)
    {
        parent::__construct($studio, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[Studio::RELATION_ANIME] = new AnimeCollection($this->whenLoaded(Studio::RELATION_ANIME), $this->query);
        $result[Studio::RELATION_RESOURCES] = new ExternalResourceCollection($this->whenLoaded(Studio::RELATION_RESOURCES), $this->query);
        $result[Studio::RELATION_IMAGES] = new ImageCollection($this->whenLoaded(Studio::RELATION_IMAGES), $this->query);

        if ($this->isAllowedField(StudioResourcePivot::ATTRIBUTE_AS)) {
            $result[StudioResourcePivot::ATTRIBUTE_AS] = $this->whenPivotLoaded(StudioResourcePivot::TABLE, fn () => $this->pivot->getAttribute(StudioResourcePivot::ATTRIBUTE_AS));
        }

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new StudioSchema();
    }
}
