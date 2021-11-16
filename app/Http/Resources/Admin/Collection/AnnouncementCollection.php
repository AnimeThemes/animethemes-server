<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Collection;

use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Schema\Schema;
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
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(
            fn (Announcement $announcement) => AnnouncementResource::make($announcement, $this->query)
        )->all();
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new AnnouncementSchema();
    }
}
