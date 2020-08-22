<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\ArtistController;
use Spatie\ResourceLinks\HasLinks;

/**
 * @OA\Schema(
 *     title="Artist",
 *     description="Artist Resource",
 *     type="object",
 *     @OA\Property(property="id",type="integer",description="Primary Key",example=53),
 *     @OA\Property(property="name",type="string",description="The Primary Name of the Artist",example="Chiwa Saito"),
 *     @OA\Property(property="alias",type="string",description="URL Slug & Model Route Key",example="chiwa_saito"),
 *     @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:55:55.000000Z"),
 *     @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:55:55.000000Z"),
 *     @OA\Property(property="songs",type="array",@OA\Items(
 *         @OA\Property(property="id",type="integer",description="Primary Key",example=3102),
 *         @OA\Property(property="title",type="string",description="The title of the song",example="staple stable"),
 *         @OA\Property(property="as",type="string",description="Used in place of the Artist name if the performance is made as a character or group/unit member",example="Hitagi Senjougahara"),
 *         @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *         @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *         @OA\Property(property="themes",type="array",@OA\Items(
 *             @OA\Property(property="id",type="integer",description="Primary Key",example=3102),
 *             @OA\Property(property="type",type="string",enum={"OP","ED"},description="Is this an OP or an ED?",example="OP"),
 *             @OA\Property(property="sequence",type="integer",description="Numeric ordering of theme",example="1"),
 *             @OA\Property(property="group",type="string",description="For separating sequences belonging to dubs, rebroadcasts, remasters, etc",example=""),
 *             @OA\Property(property="slug",type="bool",description="URL Slug",example="OP1"),
 *             @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *             @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *             @OA\Property(property="anime",type="object",
 *                 @OA\Property(property="id",type="integer",description="Primary Key",example=197),
 *                 @OA\Property(property="name",type="string",description="The Primary Title of the Anime",example="Bakemonogatari"),
 *                 @OA\Property(property="alias",type="string",description="URL Slug & Model Route Key",example="bakemonogatari"),
 *                 @OA\Property(property="year",type="integer",description="The Year in which the Anime Premiered",example=2009),
 *                 @OA\Property(property="season",type="string",enum={"Winter","Spring","Summer","Fall"},description="The Season in which the Anime Premiered",example="Summer"),
 *                 @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *                 @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *         ),
 *     )),
 *     )),
 *     @OA\Property(property="members",type="array",@OA\Items()),
 *     @OA\Property(property="groups",type="array",@OA\Items()),
 *     @OA\Property(property="resources",type="array",@OA\Items(
 *         @OA\Property(property="id",type="integer",description="Primary Key",example=3139),
 *         @OA\Property(property="link",type="string",description="The URL of the resource",example="https://myanimelist.net/people/61/"),
 *         @OA\Property(property="label",type="string",description="Used to distinguish resources that map to the same artist or anime",example=""),
 *         @OA\Property(property="type",type="string",enum={"Official Website","Twitter","aniDB","AniList","Anime-Planet","Anime News Network","Kitsu","MyAnimeList","Wiki"},description="The site that we are linking to",example="MyAnimeList"),
 *         @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:56:07.000000Z"),
 *         @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:56:07.000000Z"),
 *     ))
 * )
 */
class ArtistResource extends BaseResource
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
            'id' => $this->artist_id,
            'name' => $this->name,
            'alias' => $this->alias,
            'as' => $this->whenPivotLoaded('artist_song', function () {
                return strval($this->pivot->as);
            }, $this->whenPivotLoaded('artist_member', function () {
                return strval($this->pivot->as);
            })),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'songs' => SongResource::collection($this->whenLoaded('songs')),
            'members' => ArtistResource::collection($this->whenLoaded('members')),
            'groups' => ArtistResource::collection($this->whenLoaded('groups')),
            'resources' => ExternalResourceResource::collection($this->whenLoaded('externalResources')),
            'links' => $this->links(ArtistController::class)
        ];
    }
}
