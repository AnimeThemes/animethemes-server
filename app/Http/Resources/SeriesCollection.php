<?php

namespace App\Http\Resources;

class SeriesCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'series';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($series) {
            return SeriesResource::make($series, $this->fieldSets);
        })->all();
    }
}
