<?php

namespace App\Http\Resources;

class EntryCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'entries';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($entry) {
            return EntryResource::make($entry, $this->parser);
        })->all();
    }
}
