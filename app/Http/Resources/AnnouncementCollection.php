<?php

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceCollectionQuery;

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
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($announcement) {
            return AnnouncementResource::make($announcement, $this->parser);
        })->all();
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @var array
     */
    public static function allowedSortFields()
    {
        return [
            'announcement_id',
            'created_at',
            'updated_at',
        ];
    }
}
