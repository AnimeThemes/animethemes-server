<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceCollectionQuery;
use App\Concerns\JsonApi\PerformsResourceCollectionSearch;
use App\JsonApi\Filter\Base\CreatedAtFilter;
use App\JsonApi\Filter\Base\DeletedAtFilter;
use App\JsonApi\Filter\Base\TrashedFilter;
use App\JsonApi\Filter\Base\UpdatedAtFilter;
use App\JsonApi\Filter\Theme\ThemeGroupFilter;
use App\JsonApi\Filter\Theme\ThemeSequenceFilter;
use App\JsonApi\Filter\Theme\ThemeTypeFilter;
use Illuminate\Http\Request;

/**
 * Class ThemeCollection
 * @package App\Http\Resources
 */
class ThemeCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery;
    use PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'themes';

    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (ThemeResource $resource) {
            return $resource->parser($this->parser);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
     */
    public static function allowedIncludePaths(): array
    {
        return [
            'anime',
            'anime.images',
            'entries',
            'entries.videos',
            'song',
            'song.artists',
        ];
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return array
     */
    public static function allowedSortFields(): array
    {
        return [
            'theme_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'group',
            'type',
            'sequence',
            'slug',
            'anime_id',
            'song_id',
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return array
     */
    public static function filters(): array
    {
        return [
            ThemeGroupFilter::class,
            ThemeSequenceFilter::class,
            ThemeTypeFilter::class,
            CreatedAtFilter::class,
            UpdatedAtFilter::class,
            DeletedAtFilter::class,
            TrashedFilter::class,
        ];
    }
}
