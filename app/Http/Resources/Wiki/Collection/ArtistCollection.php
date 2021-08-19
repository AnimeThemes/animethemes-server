<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\Wiki\Artist\ArtistIdFilter;
use App\Http\Api\Filter\Wiki\Artist\ArtistNameFilter;
use App\Http\Api\Filter\Wiki\Artist\ArtistSlugFilter;
use App\Http\Api\Sort\Sort;
use App\Http\Api\Sort\Wiki\Artist\ArtistIdSort;
use App\Http\Api\Sort\Wiki\Artist\ArtistNameSort;
use App\Http\Api\Sort\Wiki\Artist\ArtistSlugSort;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Models\Wiki\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class ArtistCollection.
 */
class ArtistCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artists';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Artist::class;

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
        return $this->collection->map(function (Artist $artist) {
            return ArtistResource::make($artist, $this->query);
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
            'songs',
            'songs.animethemes',
            'songs.animethemes.anime',
            'members',
            'groups',
            'resources',
            'images',
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
                new ArtistIdSort($sortCriteria),
                new ArtistNameSort($sortCriteria),
                new ArtistSlugSort($sortCriteria),
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
                new ArtistIdFilter($filterCriteria),
                new ArtistNameFilter($filterCriteria),
                new ArtistSlugFilter($filterCriteria),
            ]
        );
    }
}
