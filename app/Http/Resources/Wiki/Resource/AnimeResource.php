<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\QueryParser;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Collection\ImageCollection;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Collection\ThemeCollection;
use App\Models\Wiki\Anime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AnimeResource.
 */
class AnimeResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'anime';

    /**
     * Create a new resource instance.
     *
     * @param Anime | MissingValue | null $anime
     * @param QueryParser $parser
     * @return void
     */
    public function __construct(Anime | MissingValue | null $anime, QueryParser $parser)
    {
        parent::__construct($anime, $parser);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->anime_id),
            'name' => $this->when($this->isAllowedField('name'), $this->name),
            'slug' => $this->when($this->isAllowedField('slug'), $this->slug),
            'year' => $this->when($this->isAllowedField('year'), $this->year),
            'season' => $this->when($this->isAllowedField('season'), strval(optional($this->season)->description)),
            'synopsis' => $this->when($this->isAllowedField('synopsis'), strval($this->synopsis)),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'synonyms' => SynonymCollection::make($this->whenLoaded('synonyms'), $this->parser),
            'themes' => ThemeCollection::make($this->whenLoaded('themes'), $this->parser),
            'series' => SeriesCollection::make($this->whenLoaded('series'), $this->parser),
            'resources' => ExternalResourceCollection::make($this->whenLoaded('resources'), $this->parser),
            'as' => $this->when($this->isAllowedField('as'), $this->whenPivotLoaded('anime_resource', function () {
                return strval($this->pivot->as);
            })),
            'images' => ImageCollection::make($this->whenLoaded('images'), $this->parser),
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
            'synonyms',
            'series',
            'themes',
            'themes.entries',
            'themes.entries.videos',
            'themes.song',
            'themes.song.artists',
            'resources',
            'images',
        ];
    }
}
