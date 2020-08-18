<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Anime",
 *     description="Anime Resource",
 *     type="object"
 * )
 */
class AnimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @OA\Property(property="id",type="integer",description="Primary Key",example=1318)
     * @OA\Property(property="name",type="string",description="The Primary Title of the Anime",example="Bakemonogatari")
     * @OA\Property(property="alias",type="string",description="URL Slug & Model Route Key",example="bakemonogatari")
     * @OA\Property(property="year",type="integer",description="The Year in which the Anime Premiered",example=2009)
     * @OA\Property(property="season",type="string",description="The Season in which the Anime Premiered",example="Summer")
     * @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="synonyms",type="object",ref="#/components/schemas/SynonymResource")
     * @OA\Property(property="themes",type="object",ref="#/components/schemas/ThemeResource")
     * @OA\Property(property="series",type="object",ref="#/components/schemas/SeriesResource")
     * @OA\Property(property="resources",type="object",ref="#/components/schemas/ExternalResourceResource")
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
            'season' => optional($this->season)->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'synonyms' => SynonymResource::collection($this->whenLoaded('synonyms')),
            'themes' => ThemeResource::collection($this->whenLoaded('themes')),
            'series' => SeriesResource::collection($this->whenLoaded('series')),
            'resources' => ExternalResourceResource::collection($this->whenLoaded('externalResources'))
        ];
    }
}
