<?php

namespace App\Http\Resources;

class AnnouncementCollection extends BaseCollection
{
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
}
