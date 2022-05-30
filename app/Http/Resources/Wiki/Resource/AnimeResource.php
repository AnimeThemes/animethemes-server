<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
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
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Anime|MissingValue|null $anime, ReadQuery $query)
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
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(Anime::ATTRIBUTE_NAME)) {
            $result[Anime::ATTRIBUTE_NAME] = $this->name;
        }

        if ($this->isAllowedField(Anime::ATTRIBUTE_SLUG)) {
            $result[Anime::ATTRIBUTE_SLUG] = $this->slug;
        }

        if ($this->isAllowedField(Anime::ATTRIBUTE_YEAR)) {
            $result[Anime::ATTRIBUTE_YEAR] = $this->year;
        }

        if ($this->isAllowedField(Anime::ATTRIBUTE_SEASON)) {
            $result[Anime::ATTRIBUTE_SEASON] = $this->season?->description;
        }

        if ($this->isAllowedField(Anime::ATTRIBUTE_SYNOPSIS)) {
            $result[Anime::ATTRIBUTE_SYNOPSIS] = $this->synopsis;
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

        $result[Anime::RELATION_SYNONYMS] = new SynonymCollection($this->whenLoaded(Anime::RELATION_SYNONYMS), $this->query);
        $result[Anime::RELATION_THEMES] = new ThemeCollection($this->whenLoaded(Anime::RELATION_THEMES), $this->query);
        $result[Anime::RELATION_SERIES] = new SeriesCollection($this->whenLoaded(Anime::RELATION_SERIES), $this->query);
        $result[Anime::RELATION_RESOURCES] = new ExternalResourceCollection($this->whenLoaded(Anime::RELATION_RESOURCES), $this->query);

        if ($this->isAllowedField(AnimeResourcePivot::ATTRIBUTE_AS)) {
            $result[AnimeResourcePivot::ATTRIBUTE_AS] = $this->whenPivotLoaded(AnimeResourcePivot::TABLE, fn () => $this->pivot->getAttribute(AnimeResourcePivot::ATTRIBUTE_AS));
        }

        $result[Anime::RELATION_IMAGES] = new ImageCollection($this->whenLoaded(Anime::RELATION_IMAGES), $this->query);
        $result[Anime::RELATION_STUDIOS] = new StudioCollection($this->whenLoaded(Anime::RELATION_STUDIOS), $this->query);

        return $result;
    }
}
