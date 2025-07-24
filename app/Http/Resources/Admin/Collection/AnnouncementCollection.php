<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Collection;

use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Http\Resources\BaseCollection;
use App\Models\Admin\Announcement;
use Illuminate\Http\Request;

class AnnouncementCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'announcements';

    /**
     * Transform the resource collection into an array.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(
            fn (Announcement $announcement) => new AnnouncementResource($announcement, $this->query)
        )->all();
    }
}
