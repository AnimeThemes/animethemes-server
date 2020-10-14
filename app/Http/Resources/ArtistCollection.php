<?php

namespace App\Http\Resources;

class ArtistCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'artists';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($artist) {
            return ArtistResource::make($artist, $this->parser);
        })->all();
    }
}
