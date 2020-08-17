<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnimeResource extends JsonResource
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
            'id' => $this->anime_id,
            'name' => $this->name,
            'alias' => $this->alias,
            'year' => $this->year,
            'season' => $this->season->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'synonyms' => SynonymResource::collection($this->whenLoaded('synonyms')),
            'themes' => ThemeResource::collection($this->whenLoaded('themes')),
            'series' => SeriesResource::collection($this->whenLoaded('series')),
            'resources' => ExternalResourceResource::collection($this->whenLoaded('externalResources'))
        ];
    }
}
