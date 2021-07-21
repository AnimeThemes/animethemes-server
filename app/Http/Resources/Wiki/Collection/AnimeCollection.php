<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Filter\Wiki\Anime\AnimeNameFilter;
use App\Http\Api\Filter\Wiki\Anime\AnimeSeasonFilter;
use App\Http\Api\Filter\Wiki\Anime\AnimeSlugFilter;
use App\Http\Api\Filter\Wiki\Anime\AnimeSynopsisFilter;
use App\Http\Api\Filter\Wiki\Anime\AnimeYearFilter;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Http\Request;

/**
 * Class AnimeCollection.
 */
class AnimeCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'anime';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Anime::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Anime $anime) {
            return AnimeResource::make($anime, $this->parser);
        })->all();
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

    /**
     * The sort field names a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedSortFields(): array
    {
        return [
            'anime_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'slug',
            'name',
            'year',
            'season',
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return string[]
     */
    public static function filters(): array
    {
        return array_merge(
            parent::filters(),
            [
                AnimeNameFilter::class,
                AnimeSlugFilter::class,
                AnimeYearFilter::class,
                AnimeSeasonFilter::class,
                AnimeSynopsisFilter::class,
            ]
        );
    }
}
