<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Collection;

use App\Http\Api\Filter\Base\CreatedAtFilter;
use App\Http\Api\Filter\Base\DeletedAtFilter;
use App\Http\Api\Filter\Base\TrashedFilter;
use App\Http\Api\Filter\Base\UpdatedAtFilter;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Http\Resources\BaseCollection;
use App\Models\Admin\Announcement;
use Illuminate\Http\Request;

/**
 * Class AnnouncementCollection.
 */
class AnnouncementCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'announcements';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Announcement::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Announcement $announcement) {
            return AnnouncementResource::make($announcement, $this->parser);
        })->all();
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return string[]
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
     * @return string[]
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
