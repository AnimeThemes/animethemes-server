<?php declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     title="Artist",
 *     description="Artist Resource",
 *     type="object",
 *     @OA\Property(property="id",type="integer",description="Primary Key",example=53),
 *     @OA\Property(property="name",type="string",description="The Primary Name of the Artist",example="Chiwa Saito"),
 *     @OA\Property(property="slug",type="string",description="URL Slug & Model Route Key",example="chiwa_saito"),
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
 *                 @OA\Property(property="slug",type="string",description="URL Slug & Model Route Key",example="bakemonogatari"),
 *                 @OA\Property(property="year",type="integer",description="The Year in which the Anime Premiered",example=2009),
 *                 @OA\Property(property="season",type="string",enum={"Winter","Spring","Summer","Fall"},description="The Season in which the Anime Premiered",example="Summer"),
 *                 @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *                 @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *             ),
 *         )),
 *     )),
 *     @OA\Property(property="members",type="array",@OA\Items()),
 *     @OA\Property(property="groups",type="array",@OA\Items()),
 *     @OA\Property(property="resources",type="array",@OA\Items(
 *         @OA\Property(property="id",type="integer",description="Primary Key",example=3139),
 *         @OA\Property(property="link",type="string",description="The URL of the resource",example="https://myanimelist.net/people/61/"),
 *         @OA\Property(property="external_id",type="integer",description="The identifier used by the external site",example="5081"),
 *         @OA\Property(property="type",type="string",enum={"Official Website","Twitter","aniDB","AniList","Anime-Planet","Anime News Network","Kitsu","MyAnimeList","Wiki"},description="The site that we are linking to",example="MyAnimeList"),
 *         @OA\Property(property="as",type="string",description="Used to distinguish resources that map to the same artist or anime",example=""),
 *         @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:56:07.000000Z"),
 *         @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:56:07.000000Z"),
 *     )),
 *     @OA\Property(property="images",type="array",@OA\Items(
 *         @OA\Property(property="id",type="integer",description="Primary Key",example=1018),
 *         @OA\Property(property="path",type="string",description="The path of the Image in storage",example="anime/bakemonogatari.png"),
 *         @OA\Property(property="facet",type="string",enum={"Small Cover","Large Cover"},description="THe component of the page the image is intended for",example="Small Cover"),
 *         @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *         @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *     ))
 * )
 */
class ArtistResource extends BaseResource
{
    use PerformsResourceQuery;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'artist';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->artist_id),
            'name' => $this->when($this->isAllowedField('name'), $this->name),
            'slug' => $this->when($this->isAllowedField('slug'), $this->slug),
            'as' => $this->when($this->isAllowedField('as'), $this->whenPivotLoaded('artist_song', function () {
                return strval($this->pivot->as);
            }, $this->whenPivotLoaded('artist_member', function () {
                return strval($this->pivot->as);
            }, $this->whenPivotLoaded('artist_resource', function () {
                return strval($this->pivot->as);
            })))),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'songs' => SongCollection::make($this->whenLoaded('songs'), $this->parser),
            'members' => ArtistCollection::make($this->whenLoaded('members'), $this->parser),
            'groups' => ArtistCollection::make($this->whenLoaded('groups'), $this->parser),
            'resources' => ExternalResourceCollection::make($this->whenLoaded('externalResources'), $this->parser),
            'images' => ImageCollection::make($this->whenLoaded('images'), $this->parser),
        ];
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
     */
    public static function allowedIncludePaths(): array
    {
        return [
            'songs',
            'songs.themes',
            'songs.themes.anime',
            'members',
            'groups',
            'externalResources',
            'images',
        ];
    }

    /**
     * Resolve the related collection resource from the relation name.
     * We are assuming a convention of "{Relation}Collection".
     *
     * @param string $allowedIncludePath
     * @return string
     */
    protected static function relation(string $allowedIncludePath): string
    {
        $relatedModel = Str::ucfirst(Str::singular(Str::of($allowedIncludePath)->explode('.')->last()));

        // Member and Group attributes do not follow convention
        if ($relatedModel === 'Member' || $relatedModel === 'Group') {
            $relatedModel = 'Artist';
        }

        return "\\App\\Http\\Resources\\{$relatedModel}Collection";
    }
}
