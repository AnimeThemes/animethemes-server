<?php

namespace App\Http\Resources;

class ThemeCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'themes';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($theme) {
            return ThemeResource::make($theme, $this->fieldSets);
        })->all();
    }
}
