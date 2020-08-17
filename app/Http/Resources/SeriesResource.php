<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->series_id,
            'name' => $this->name,
            'alias' => $this->alias,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'anime' => AnimeResource::collection($this->whenLoaded('anime'))
        ];
    }
}
