<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Entry",
 *     description="Entry Resource",
 *     type="object"
 * )
 */
class EntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @OA\Property(property="id",type="integer",description="Primary Key",example=1850)
     * @OA\Property(property="version",type="integer",description="The Version number of the Theme",example=2)
     * @OA\Property(property="episodes",type="string",description="The range(s) of episodes that the theme entry is used",example="1-2, 12")
     * @OA\Property(property="nsfw",type="bool",description="Does the entry include Not Safe For Work content?",example="true")
     * @OA\Property(property="spoiler",type="bool",description="Does the entry include content that spoils the show?",example="false")
     * @OA\Property(property="notes",type="string",description="Any additional information not included in other fields that may be useful",example="Different character at 1:05")
     * @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="anime",type="object",ref="#/components/schemas/AnimeResource")
     * @OA\Property(property="theme",type="object",ref="#/components/schemas/ThemeResource")
     * @OA\Property(property="videos",type="object",ref="#/components/schemas/VideoResource")
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->entry_id,
            'version' => $this->version,
            'episodes' => $this->episodes,
            'nsfw' => $this->nsfw,
            'spoiler' => $this->spoiler,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'anime' => AnimeResource::make($this->whenLoaded('anime')),
            'theme' => ThemeResource::make($this->whenLoaded('theme')),
            'videos' => VideoResource::collection($this->whenLoaded('videos')),
        ];
    }
}
