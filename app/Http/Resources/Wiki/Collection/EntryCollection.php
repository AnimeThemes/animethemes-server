<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\Wiki\Entry\EntryEpisodesFilter;
use App\Http\Api\Filter\Wiki\Entry\EntryIdFilter;
use App\Http\Api\Filter\Wiki\Entry\EntryNotesFilter;
use App\Http\Api\Filter\Wiki\Entry\EntryNsfwFilter;
use App\Http\Api\Filter\Wiki\Entry\EntrySpoilerFilter;
use App\Http\Api\Filter\Wiki\Entry\EntryVersionFilter;
use App\Http\Api\Sort\Sort;
use App\Http\Api\Sort\Wiki\Entry\EntryEpisodesSort;
use App\Http\Api\Sort\Wiki\Entry\EntryIdSort;
use App\Http\Api\Sort\Wiki\Entry\EntryNotesSort;
use App\Http\Api\Sort\Wiki\Entry\EntryNsfwSort;
use App\Http\Api\Sort\Wiki\Entry\EntrySpoilerSort;
use App\Http\Api\Sort\Wiki\Entry\EntryVersionSort;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\EntryResource;
use App\Models\Wiki\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class EntryCollection.
 */
class EntryCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'entries';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Entry::class;

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
        return $this->collection->map(function (Entry $entry) {
            return EntryResource::make($entry, $this->query);
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
            'theme',
            'theme.anime',
            'videos',
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
                new EntryIdSort($sortCriteria),
                new EntryVersionSort($sortCriteria),
                new EntryEpisodesSort($sortCriteria),
                new EntryNsfwSort($sortCriteria),
                new EntrySpoilerSort($sortCriteria),
                new EntryNotesSort($sortCriteria),
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
                new EntryIdFilter($filterCriteria),
                new EntryVersionFilter($filterCriteria),
                new EntryEpisodesFilter($filterCriteria),
                new EntryNsfwFilter($filterCriteria),
                new EntrySpoilerFilter($filterCriteria),
                new EntryNotesFilter($filterCriteria),
            ]
        );
    }
}
