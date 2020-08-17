<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThemeResource extends JsonResource
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
            'id' => $this->theme_id,
            'type' => $this->type->description,
            'sequence' => $this->sequence,
            'group' => $this->group,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'anime' => AnimeResource::make($this->whenLoaded('anime')),
            'song' => SongResource::make($this->whenLoaded('song')),
            'entries' => EntryResource::collection($this->whenLoaded('entries'))
        ];
    }
}
