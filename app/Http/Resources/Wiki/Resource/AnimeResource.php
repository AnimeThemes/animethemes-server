<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Pivots\AnimeResource as AnimeResourcePivot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AnimeResource.
 *
 * @mixin Anime
 */
class AnimeResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'anime';

    /**
     * Create a new resource instance.
     *
     * @param  Anime | MissingValue | null  $anime
     * @param  Query  $query
     * @return void
     */
    public function __construct(Anime|MissingValue|null $anime, Query $query)
    {
        parent::__construct($anime, $query);
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
        return [
            BaseResource::ATTRIBUTE_ID => $this->when($this->isAllowedField(BaseResource::ATTRIBUTE_ID), $this->getKey()),
            Anime::ATTRIBUTE_NAME => $this->when($this->isAllowedField(Anime::ATTRIBUTE_NAME), $this->name),
            Anime::ATTRIBUTE_SLUG => $this->when($this->isAllowedField(Anime::ATTRIBUTE_SLUG), $this->slug),
            Anime::ATTRIBUTE_YEAR => $this->when($this->isAllowedField(Anime::ATTRIBUTE_YEAR), $this->year),
            Anime::ATTRIBUTE_SEASON => $this->when($this->isAllowedField(Anime::ATTRIBUTE_SEASON), $this->season?->description),
            Anime::ATTRIBUTE_SYNOPSIS => $this->when($this->isAllowedField(Anime::ATTRIBUTE_SYNOPSIS), $this->synopsis),
            BaseModel::ATTRIBUTE_CREATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT), $this->created_at),
            BaseModel::ATTRIBUTE_UPDATED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT), $this->updated_at),
            BaseModel::ATTRIBUTE_DELETED_AT => $this->when($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT), $this->deleted_at),
            Anime::RELATION_SYNONYMS => SynonymCollection::make($this->whenLoaded(Anime::RELATION_SYNONYMS), $this->query),
            Anime::RELATION_THEMES => ThemeCollection::make($this->whenLoaded(Anime::RELATION_THEMES), $this->query),
            Anime::RELATION_SERIES => SeriesCollection::make($this->whenLoaded(Anime::RELATION_SERIES), $this->query),
            Anime::RELATION_RESOURCES => ExternalResourceCollection::make($this->whenLoaded(Anime::RELATION_RESOURCES), $this->query),
            AnimeResourcePivot::ATTRIBUTE_AS => $this->when(
                $this->isAllowedField(AnimeResourcePivot::ATTRIBUTE_AS),
                $this->whenPivotLoaded(AnimeResourcePivot::TABLE, fn () => $this->pivot->getAttribute(AnimeResourcePivot::ATTRIBUTE_AS))
            ),
            Anime::RELATION_IMAGES => ImageCollection::make($this->whenLoaded(Anime::RELATION_IMAGES), $this->query),
            Anime::RELATION_STUDIOS => StudioCollection::make($this->whenLoaded(Anime::RELATION_STUDIOS), $this->query),
        ];
    }
}
