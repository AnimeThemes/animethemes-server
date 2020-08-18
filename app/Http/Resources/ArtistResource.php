<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Artist",
 *     description="Artist Resource",
 *     type="object"
 * )
 */
class ArtistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @OA\Property(property="id",type="integer",description="Primary Key",example=1318)
     * @OA\Property(property="name",type="string",description="The Primary Name of the Artist",example="Chiwa Saito")
     * @OA\Property(property="alias",type="string",description="URL Slug & Model Route Key",example="chiwa_saito")
     * @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="members",type="object",ref="#/components/schemas/ArtistResource")
     * @OA\Property(property="groups",type="object",ref="#/components/schemas/ArtistResource")
     * @OA\Property(property="resources",type="object",ref="#/components/schemas/ExternalResourceResource")
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
