<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Resource",
 *     description="External Resource",
 *     type="object"
 * )
 */
class ExternalResourceResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @OA\Property(property="id",type="integer",description="Primary Key",example=1850)
     * @OA\Property(property="label",type="string",description="Used to distinguish resources that map to the same artist or anime",example="S2")
     * @OA\Property(property="link",type="string",description="The URL of the resource",example="https://myanimelist.net/people/8/")
     * @OA\Property(property="type",type="string",description="The site that we are linking to",example="MyAnimeList")
     * @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="artists",type="object",ref="#/components/schemas/ArtistResource")
     * @OA\Property(property="anime",type="object",ref="#/components/schemas/AnimeResource")
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
            'type' => $this->type->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'artists' => ArtistResource::collection($this->whenLoaded('artists')),
            'anime' => AnimeResource::collection($this->whenLoaded('anime'))
        ];
    }
}
