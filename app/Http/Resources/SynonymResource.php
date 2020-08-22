<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\SynonymController;
use Spatie\ResourceLinks\HasLinks;

/**
 * @OA\Schema(
 *     title="Synonym",
 *     description="Synonym Resource",
 *     type="object",
 *     @OA\Property(property="id",type="integer",description="Primary Key",example=1464),
 *     @OA\Property(property="text",type="string",description="For alternative titles, licensed titles, common abbreviations and/or shortenings",example="Monstory"),
 *     @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *     @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *     @OA\Property(property="anime",type="object",
 *         @OA\Property(property="id",type="integer",description="Primary Key",example=197),
 *         @OA\Property(property="name",type="string",description="The Primary Title of the Anime",example="Bakemonogatari"),
 *         @OA\Property(property="alias",type="string",description="URL Slug & Model Route Key",example="bakemonogatari"),
 *         @OA\Property(property="year",type="integer",description="The Year in which the Anime Premiered",example=2009),
 *         @OA\Property(property="season",type="string",enum={"Winter","Spring","Summer","Fall"},description="The Season in which the Anime Premiered",example="Summer"),
 *         @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *         @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *     )
 * )
 */
class SynonymResource extends BaseResource
{

    use HasLinks;

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
            'anime' => AnimeResource::make($this->whenLoaded('anime')),
            'links' => $this->links(SynonymController::class)
        ];
    }
}
