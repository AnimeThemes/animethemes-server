<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArtistResource extends JsonResource
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
            'id' => $this->artist_id,
            'name' => $this->name,
            'alias' => $this->alias,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'songs' => SongResource::collection($this->whenLoaded('songs')),
            'members' => ArtistResource::collection($this->whenLoaded('members')),
            'groups' => ArtistResource::collection($this->whenLoaded('groups')),
            'resources' => ExternalResourceResource::collection($this->whenLoaded('externalResources'))
        ];
    }
}
