<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
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
            'id' => $this->entry_id,
            'version' => $this->version,
            'episodes' => $this->episodes,
            'nsfw' => $this->nsfw,
            'spoiler' => $this->spoiler,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'anime' => AnimeResource::make($this->whenLoaded('anime')),
            'theme' => ThemeResource::make($this->whenLoaded('theme')),
            'videos' => VideoResource::collection($this->whenLoaded('videos')),
        ];
    }
}
