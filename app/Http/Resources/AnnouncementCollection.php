<?php declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceCollectionQuery;
use App\JsonApi\Filter\Base\CreatedAtFilter;
use App\JsonApi\Filter\Base\DeletedAtFilter;
use App\JsonApi\Filter\Base\TrashedFilter;
use App\JsonApi\Filter\Base\UpdatedAtFilter;
use Illuminate\Http\Request;

/**
 * Class AnnouncementCollection
 * @package App\Http\Resources
 */
class AnnouncementCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'announcements';

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (AnnouncementResource $resource) {
            return $resource->parser($this->parser);
        })->all();
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return array
     */
    public static function allowedSortFields(): array
    {
        return [
            'announcement_id',
            'created_at',
            'updated_at',
            'deleted_at',
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
