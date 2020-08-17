<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
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
            'id' => $this->song_id,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'themes' => ThemeResource::collection($this->whenLoaded('themes')),
            'artists' => ArtistResource::collection($this->whenLoaded('artists')),
        ];
    }
}
