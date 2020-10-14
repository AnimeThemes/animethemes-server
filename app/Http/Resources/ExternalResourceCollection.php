<?php

namespace App\Http\Resources;

class ExternalResourceCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'resources';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($resource) {
            return ExternalResourceResource::make($resource, $this->parser);
        })->all();
    }
}
