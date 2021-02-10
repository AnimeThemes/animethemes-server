<?php

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceQuery;

/**
 * @OA\Schema(
 *     title="Resource",
 *     description="External Resource",
 *     type="object",
 *     @OA\Property(property="id",type="integer",description="Primary Key",example=1018),
 *     @OA\Property(property="external_id",type="integer",description="The identifier used by the external site",example="5081"),
 *     @OA\Property(property="link",type="string",description="The URL of the resource",example="https://myanimelist.net/anime/5081/"),
 *     @OA\Property(property="type",type="string",enum={"Official Website","Twitter","aniDB","AniList","Anime-Planet","Anime News Network","Kitsu","MyAnimeList","Wiki"},description="The site that we are linking to",example="MyAnimeList"),
 *     @OA\Property(property="as",type="string",description="Used to distinguish resources that map to the same artist or anime",example=""),
 *     @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *     @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *     @OA\Property(property="artists",type="array",@OA\Items()),
 *     @OA\Property(property="anime",type="array",@OA\Items(
 *         @OA\Property(property="id",type="integer",description="Primary Key",example=197),
 *         @OA\Property(property="name",type="string",description="The Primary Title of the Anime",example="Bakemonogatari"),
 *         @OA\Property(property="slug",type="string",description="URL Slug & Model Route Key",example="bakemonogatari"),
 *         @OA\Property(property="year",type="integer",description="The Year in which the Anime Premiered",example=2009),
 *         @OA\Property(property="season",type="string",enum={"Winter","Spring","Summer","Fall"},description="The Season in which the Anime Premiered",example="Summer"),
 *         @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *         @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *     ))
 * )
 */
class ExternalResourceResource extends BaseResource
{
    use PerformsResourceQuery;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = null;

    /**
     * The name of the resource in the field set mapping.
     *
     * @var string
     */
    public static $resourceType = 'resource';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->resource_id),
            'link' => $this->when($this->isAllowedField('link'), $this->link),
            'external_id' => $this->when($this->isAllowedField('external_id'), is_null($this->external_id) ? '' : $this->external_id),
            'site' => $this->when($this->isAllowedField('site'), strval(optional($this->site)->description)),
            'as' => $this->when($this->isAllowedField('as'), $this->whenPivotLoaded('anime_resource', function () {
                return strval($this->pivot->as);
            }, $this->whenPivotLoaded('artist_resource', function () {
                return strval($this->pivot->as);
            }))),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'artists' => ArtistCollection::make($this->whenLoaded('artists'), $this->parser),
            'anime' => AnimeCollection::make($this->whenLoaded('anime'), $this->parser),
        ];
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
     */
    public static function allowedIncludePaths()
    {
        return [
            'anime',
            'artists',
        ];
    }
}
