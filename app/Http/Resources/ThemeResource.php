<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Theme",
 *     description="Theme Resource",
 *     type="object"
 * )
 */
class ThemeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @OA\Property(property="id",type="integer",description="Primary Key",example=1850)
     * @OA\Property(property="type",type="string",description="Is this an OP or an ED?",example="OP")
     * @OA\Property(property="sequence",type="integer",description="Numeric ordering of theme",example="1")
     * @OA\Property(property="group",type="string",description="For separating sequences belonging to dubs, rebroadcasts, remasters, etc",example="AT-X Broadcast")
     * @OA\Property(property="slug",type="bool",description="URL Slug",example="ED1")
     * @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="anime",type="object",ref="#/components/schemas/AnimeResource")
     * @OA\Property(property="song",type="object",ref="#/components/schemas/SongResource")
     * @OA\Property(property="entries",type="object",ref="#/components/schemas/EntryResource")
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
