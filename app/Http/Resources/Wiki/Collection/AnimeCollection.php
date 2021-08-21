<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\Wiki\Anime\AnimeIdFilter;
use App\Http\Api\Filter\Wiki\Anime\AnimeNameFilter;
use App\Http\Api\Filter\Wiki\Anime\AnimeSeasonFilter;
use App\Http\Api\Filter\Wiki\Anime\AnimeSlugFilter;
use App\Http\Api\Filter\Wiki\Anime\AnimeSynopsisFilter;
use App\Http\Api\Filter\Wiki\Anime\AnimeYearFilter;
use App\Http\Api\Sort\Sort;
use App\Http\Api\Sort\Wiki\Anime\AnimeIdSort;
use App\Http\Api\Sort\Wiki\Anime\AnimeNameSort;
use App\Http\Api\Sort\Wiki\Anime\AnimeSeasonSort;
use App\Http\Api\Sort\Wiki\Anime\AnimeSlugSort;
use App\Http\Api\Sort\Wiki\Anime\AnimeSynopsisSort;
use App\Http\Api\Sort\Wiki\Anime\AnimeYearSort;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
            return AnimeResource::make($anime, $this->query);
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

    /**
     * The sorts that can be applied by the client for this resource.
     *
     * @param Collection<Criteria> $sortCriteria
     * @return Sort[]
     */
    public static function sorts(Collection $sortCriteria): array
    {
        return array_merge(
            parent::sorts($sortCriteria),
            [
                new AnimeIdSort($sortCriteria),
                new AnimeNameSort($sortCriteria),
                new AnimeSlugSort($sortCriteria),
                new AnimeYearSort($sortCriteria),
                new AnimeSeasonSort($sortCriteria),
                new AnimeSynopsisSort($sortCriteria),
            ]
        );
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @param Collection<FilterCriteria> $filterCriteria
     * @return Filter[]
     */
    public static function filters(Collection $filterCriteria): array
    {
        return array_merge(
            parent::filters($filterCriteria),
            [
                new AnimeIdFilter($filterCriteria),
                new AnimeNameFilter($filterCriteria),
                new AnimeSlugFilter($filterCriteria),
                new AnimeYearFilter($filterCriteria),
                new AnimeSeasonFilter($filterCriteria),
                new AnimeSynopsisFilter($filterCriteria),
            ]
        );
    }
}
