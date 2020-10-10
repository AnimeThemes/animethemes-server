<?php

namespace App\Http\Resources;

class SynonymCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'synonyms';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($synonym) {
            return SynonymResource::make($synonym, $this->fieldSets);
        })->all();
    }
}
