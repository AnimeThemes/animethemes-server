<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Song",
 *     description="Song Resource",
 *     type="object"
 * )
 */
class SongResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @OA\Property(property="id",type="integer",description="Primary Key",example=1850)
     * @OA\Property(property="title",type="string",description="The title of the song",example="stable staple")
     * @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="themes",type="object",ref="#/components/schemas/ThemeResource")
     * @OA\Property(property="artists",type="object",ref="#/components/schemas/ArtistResource")
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
