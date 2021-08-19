<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\Wiki\Anime\Theme\ThemeGroupFilter;
use App\Http\Api\Filter\Wiki\Anime\Theme\ThemeIdFilter;
use App\Http\Api\Filter\Wiki\Anime\Theme\ThemeSequenceFilter;
use App\Http\Api\Filter\Wiki\Anime\Theme\ThemeSlugFilter;
use App\Http\Api\Filter\Wiki\Anime\Theme\ThemeTypeFilter;
use App\Http\Api\Sort\Sort;
use App\Http\Api\Sort\Wiki\Anime\Theme\ThemeGroupSort;
use App\Http\Api\Sort\Wiki\Anime\Theme\ThemeIdSort;
use App\Http\Api\Sort\Wiki\Anime\Theme\ThemeSequenceSort;
use App\Http\Api\Sort\Wiki\Anime\Theme\ThemeSlugSort;
use App\Http\Api\Sort\Wiki\Anime\Theme\ThemeTypeSort;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ThemeCollection.
 */
class ThemeCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animethemes';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = AnimeTheme::class;

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
        return $this->collection->map(function (AnimeTheme $theme) {
            return ThemeResource::make($theme, $this->query);
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
            'anime',
            'anime.images',
            'animethemeentries',
            'animethemeentries.videos',
            'song',
            'song.artists',
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
                new ThemeIdSort($sortCriteria),
                new ThemeTypeSort($sortCriteria),
                new ThemeSequenceSort($sortCriteria),
                new ThemeGroupSort($sortCriteria),
                new ThemeSlugSort($sortCriteria),
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
                new ThemeIdFilter($filterCriteria),
                new ThemeTypeFilter($filterCriteria),
                new ThemeSequenceFilter($filterCriteria),
                new ThemeGroupFilter($filterCriteria),
                new ThemeSlugFilter($filterCriteria),
            ]
        );
    }
}
