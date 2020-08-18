<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Series",
 *     description="Series Resource",
 *     type="object"
 * )
 */
class SeriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @OA\Property(property="id",type="integer",description="Primary Key",example=1318)
     * @OA\Property(property="name",type="string",description="The Primary Name of the Series",example="Monogatari")
     * @OA\Property(property="alias",type="string",description="URL Slug & Model Route Key",example="monogatari")
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
            'id' => $this->series_id,
            'name' => $this->name,
            'alias' => $this->alias,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'anime' => AnimeResource::collection($this->whenLoaded('anime'))
        ];
    }
}
