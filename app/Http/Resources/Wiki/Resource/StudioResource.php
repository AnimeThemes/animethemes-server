<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Models\BaseModel;
use App\Models\Wiki\Studio;
use App\Pivots\StudioResource as StudioResourcePivot;
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
     * @param  Query  $query
     * @return void
     */
    public function __construct(Studio|MissingValue|null $studio, Query $query)
    {
        parent::__construct($studio, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(Studio::ATTRIBUTE_NAME)) {
            $result[Studio::ATTRIBUTE_NAME] = $this->name;
        }

        if ($this->isAllowedField(Studio::ATTRIBUTE_SLUG)) {
            $result[Studio::ATTRIBUTE_SLUG] = $this->slug;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        $result[Studio::RELATION_ANIME] = AnimeCollection::make($this->whenLoaded(Studio::RELATION_ANIME), $this->query);
        $result[Studio::RELATION_RESOURCES] = ExternalResourceCollection::make($this->whenLoaded(Studio::RELATION_RESOURCES), $this->query);

        if ($this->isAllowedField(StudioResourcePivot::ATTRIBUTE_AS)) {
            $result[StudioResourcePivot::ATTRIBUTE_AS] = $this->whenPivotLoaded(StudioResourcePivot::TABLE, fn () => $this->pivot->getAttribute(StudioResourcePivot::ATTRIBUTE_AS));
        }

        return $result;
    }
}
