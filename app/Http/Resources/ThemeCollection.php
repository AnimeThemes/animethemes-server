<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\Http\Api\PerformsResourceCollectionQuery;
use App\Concerns\Http\Api\PerformsResourceCollectionSearch;
use App\Http\Api\Filter\Base\CreatedAtFilter;
use App\Http\Api\Filter\Base\DeletedAtFilter;
use App\Http\Api\Filter\Base\TrashedFilter;
use App\Http\Api\Filter\Base\UpdatedAtFilter;
use App\Http\Api\Filter\Theme\ThemeGroupFilter;
use App\Http\Api\Filter\Theme\ThemeSequenceFilter;
use App\Http\Api\Filter\Theme\ThemeTypeFilter;
use Illuminate\Http\Request;

/**
 * Class ThemeCollection.
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
     * @return string[]
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
     * @return string[]
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
     * @return string[]
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
