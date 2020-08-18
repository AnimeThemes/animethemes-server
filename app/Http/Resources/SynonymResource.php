<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Synonym",
 *     description="Synonym Resource",
 *     type="object"
 * )
 */
class SynonymResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @OA\Property(property="id",type="integer",description="Primary Key",example=1850)
     * @OA\Property(property="text",type="string",description="For alternative titles, licensed titles, common abbreviations and/or shortenings",example="Monstory")
     * @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="anime",type="object",ref="#/components/schemas/AnimeResource")
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
