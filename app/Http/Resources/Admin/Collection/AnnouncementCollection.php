<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Collection;

use App\Http\Api\Filter\Admin\Announcement\AnnouncementContentFilter;
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
     * @var string|null
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Announcement $announcement) {
            return AnnouncementResource::make($announcement, $this->parser);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [];
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
        return array_merge(
            parent::filters(),
            [
                AnnouncementContentFilter::class,
            ]
        );
    }
}
