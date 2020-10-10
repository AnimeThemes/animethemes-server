<?php

namespace App\Http\Resources;

class AnimeCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'anime';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($anime) {
            return AnimeResource::make($anime, $this->fieldSets);
        })->all();
    }
}
