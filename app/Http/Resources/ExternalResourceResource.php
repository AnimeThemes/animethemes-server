<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExternalResourceResource extends JsonResource
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
            'id' => $this->resource_id,
            'link' => $this->link,
            'label' => $this->label,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'artists' => ArtistResource::collection($this->whenLoaded('artists')),
            'anime' => AnimeResource::collection($this->whenLoaded('anime'))
        ];
    }
}
