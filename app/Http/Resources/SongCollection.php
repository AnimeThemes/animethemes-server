<?php declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceCollectionQuery;
use App\Concerns\JsonApi\PerformsResourceCollectionSearch;
use App\JsonApi\Filter\Base\CreatedAtFilter;
use App\JsonApi\Filter\Base\DeletedAtFilter;
use App\JsonApi\Filter\Base\TrashedFilter;
use App\JsonApi\Filter\Base\UpdatedAtFilter;
use Illuminate\Http\Request;

/**
 * Class SongCollection
 * @package App\Http\Resources
 */
class SongCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery;
    use PerformsResourceCollectionSearch;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'songs';

    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (SongResource $resource) {
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
            'themes',
            'themes.anime',
            'artists',
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
            'song_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'title',
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
            CreatedAtFilter::class,
            UpdatedAtFilter::class,
            DeletedAtFilter::class,
            TrashedFilter::class,
        ];
    }
}
