<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SynonymResource extends JsonResource
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
            'id' => $this->synonym_id,
            'text' => $this->text,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'anime' => AnimeResource::make($this->whenLoaded('anime'))
        ];
    }
}
