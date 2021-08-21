<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Models\Wiki\Anime;
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
     * @param Anime | MissingValue | null $anime
     * @param Query $query
     * @return void
     */
    public function __construct(Anime | MissingValue | null $anime, Query $query)
    {
        parent::__construct($anime, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->anime_id),
            'name' => $this->when($this->isAllowedField('name'), $this->name),
            'slug' => $this->when($this->isAllowedField('slug'), $this->slug),
            'year' => $this->when($this->isAllowedField('year'), $this->year),
            'season' => $this->when($this->isAllowedField('season'), $this->season?->description),
            'synopsis' => $this->when($this->isAllowedField('synopsis'), $this->synopsis),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'animesynonyms' => SynonymCollection::make($this->whenLoaded('animesynonyms'), $this->query),
            'animethemes' => ThemeCollection::make($this->whenLoaded('animethemes'), $this->query),
            'series' => SeriesCollection::make($this->whenLoaded('series'), $this->query),
            'resources' => ExternalResourceCollection::make($this->whenLoaded('resources'), $this->query),
            'as' => $this->when($this->isAllowedField('as'), $this->whenPivotLoaded('anime_resource', function () {
                return $this->pivot->getAttribute('as');
            })),
            'images' => ImageCollection::make($this->whenLoaded('images'), $this->query),
            'studios' => StudioCollection::make($this->whenLoaded('studios'), $this->query),
        ];
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [
            'animesynonyms',
            'series',
            'animethemes',
            'animethemes.animethemeentries',
            'animethemes.animethemeentries.videos',
            'animethemes.song',
            'animethemes.song.artists',
            'resources',
            'images',
            'studios',
        ];
    }
}
