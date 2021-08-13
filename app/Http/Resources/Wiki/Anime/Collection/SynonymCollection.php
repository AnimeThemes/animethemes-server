<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Anime\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\Wiki\Anime\Synonym\SynonymIdFilter;
use App\Http\Api\Filter\Wiki\Anime\Synonym\SynonymTextFilter;
use App\Http\Api\Sort\Sort;
use App\Http\Api\Sort\Wiki\Anime\Synonym\SynonymIdSort;
use App\Http\Api\Sort\Wiki\Anime\Synonym\SynonymTextSort;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Models\Wiki\Anime\Synonym;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class SynonymCollection.
 */
class SynonymCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'synonyms';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Synonym::class;

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
        return $this->collection->map(function (Synonym $synonym) {
            return SynonymResource::make($synonym, $this->query);
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
                new SynonymIdSort($sortCriteria),
                new SynonymTextSort($sortCriteria),
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
                new SynonymIdFilter($filterCriteria),
                new SynonymTextFilter($filterCriteria),
            ]
        );
    }
}
