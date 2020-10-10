<?php

namespace App\Http\Resources;

class VideoCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'videos';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($video) {
            return VideoResource::make($video, $this->fieldSets);
        })->all();
    }
}
