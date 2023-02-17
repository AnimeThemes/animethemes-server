<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Models\Wiki\Anime;
use App\Pivots\Wiki\AnimeResource as AnimeResourcePivot;
use Illuminate\Http\Request;

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
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

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

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new AnimeSchema();
    }
}
