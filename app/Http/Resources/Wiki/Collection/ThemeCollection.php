<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Concerns\Http\Resources\PerformsResourceCollectionSearch;
use App\Http\Api\Filter\Base\CreatedAtFilter;
use App\Http\Api\Filter\Base\DeletedAtFilter;
use App\Http\Api\Filter\Base\TrashedFilter;
use App\Http\Api\Filter\Base\UpdatedAtFilter;
use App\Http\Api\Filter\Wiki\Theme\ThemeGroupFilter;
use App\Http\Api\Filter\Wiki\Theme\ThemeSequenceFilter;
use App\Http\Api\Filter\Wiki\Theme\ThemeTypeFilter;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\ThemeResource;
use App\Models\Wiki\Theme;
use Illuminate\Http\Request;

/**
 * Class ThemeCollection.
 */
class ThemeCollection extends BaseCollection
{
    use PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'themes';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Theme::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Theme $theme) {
            return ThemeResource::make($theme, $this->parser);
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
