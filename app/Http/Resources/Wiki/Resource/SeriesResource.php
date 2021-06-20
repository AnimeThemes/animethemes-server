<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\QueryParser;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Models\Wiki\Series;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * @OA\Schema(
 *     title="Series",
 *     description="Series Resource",
 *     type="object",
 *     @OA\Property(property="id",type="integer",description="Primary Key",example=114),
 *     @OA\Property(property="name",type="string",description="The Primary Name of the Series",example="Monogatari"),
 *     @OA\Property(property="slug",type="string",description="URL Slug & Model Route Key",example="monogatari"),
 *     @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T06:53:20.000000Z"),
 *     @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T06:53:20.000000Z"),
 *     @OA\Property(property="anime",type="array",@OA\Items(
 *         @OA\Property(property="id",type="integer",description="Primary Key",example=197),
 *         @OA\Property(property="name",type="string",description="The Primary Title of the Anime",example="Bakemonogatari"),
 *         @OA\Property(property="slug",type="string",description="URL Slug & Model Route Key",example="bakemonogatari"),
 *         @OA\Property(property="year",type="integer",description="The Year in which the Anime Premiered",example=2009),
 *         @OA\Property(property="season",type="string",enum={"Winter","Spring","Summer","Fall"},description="The Season in which the Anime Premiered",example="Summer"),
 *         @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *         @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *         @OA\Property(property="synonyms",type="array",@OA\Items(
 *             @OA\Property(property="id",type="integer",description="Primary Key",example=1464),
 *             @OA\Property(property="text",type="string",description="For alternative titles, licensed titles, common abbreviations and/or shortenings",example="Monstory"),
 *             @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *             @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:43:02.000000Z")
 *         )),
 *         @OA\Property(property="themes",type="array",@OA\Items(
 *             @OA\Property(property="id",type="integer",description="Primary Key",example=3102),
 *             @OA\Property(property="type",type="string",enum={"OP","ED"},description="Is this an OP or an ED?",example="OP"),
 *             @OA\Property(property="sequence",type="integer",description="Numeric ordering of theme",example="1"),
 *             @OA\Property(property="group",type="string",description="For separating sequences belonging to dubs, rebroadcasts, remasters, etc",example=""),
 *             @OA\Property(property="slug",type="bool",description="URL Slug",example="OP1"),
 *             @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *             @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *             @OA\Property(property="song",type="object",
 *                 @OA\Property(property="id",type="integer",description="Primary Key",example=3102),
 *                 @OA\Property(property="title",type="string",description="The title of the song",example="staple stable"),
 *                 @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *                 @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *                 @OA\Property(property="artists",type="array",@OA\Items(
 *                     @OA\Property(property="id",type="integer",description="Primary Key",example=53),
 *                     @OA\Property(property="name",type="string",description="The Primary Name of the Artist",example="Chiwa Saito"),
 *                     @OA\Property(property="slug",type="string",description="URL Slug & Model Route Key",example="chiwa_saito"),
 *                     @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:55:55.000000Z"),
 *                     @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:55:55.000000Z"),
 *                 ))
 *             ),
 *             @OA\Property(property="entries",type="array",@OA\Items(
 *                 @OA\Property(property="id",type="integer",description="Primary Key",example=3518),
 *                 @OA\Property(property="version",type="integer",description="The Version number of the Theme",example=""),
 *                 @OA\Property(property="episodes",type="string",description="The range(s) of episodes that the theme entry is used",example="1-2, 12"),
 *                 @OA\Property(property="nsfw",type="bool",description="Does the entry include Not Safe For Work content?",example="false"),
 *                 @OA\Property(property="spoiler",type="bool",description="Does the entry include content that spoils the show?",example="false"),
 *                 @OA\Property(property="notes",type="string",description="Any additional information not included in other fields that may be useful",example=""),
 *                 @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *                 @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *                 @OA\Property(property="videos",type="array",@OA\Items(
 *                     @OA\Property(property="id",type="integer",description="Primary Key",example=2615),
 *                     @OA\Property(property="basename",type="string",description="The basename of the Video",example="Bakemonogatari-OP1.webm"),
 *                     @OA\Property(property="filename",type="string",description="The filename of the Video",example="Bakemonogatari-OP1"),
 *                     @OA\Property(property="path",type="string",description="The path of the Video in storage",example="2009/Summer/Bakemonogatari-OP1.webm"),
 *                     @OA\Property(property="resolution",type="integer",description="Frame height of the video",example=1080),
 *                     @OA\Property(property="nc",type="bool",description="Is the video creditless?",example="true"),
 *                     @OA\Property(property="subbed",type="bool",description="Does the video include subtitles of dialogue?",example="false"),
 *                     @OA\Property(property="lyrics",type="bool",description="Does the video include subtitles for song lyrics?",example="false"),
 *                     @OA\Property(property="uncen",type="bool",description="Is the video an uncensored version of a censored sequence?",example="false"),
 *                     @OA\Property(property="source",type="string",enum={"WEB","RAW","BD","DVD","VHS"},description="Where did this video come from?",example="BD"),
 *                     @OA\Property(property="overlap",type="string",enum={"None","Transition","Over"},description="The degree to which the sequence and episode content overlap",example="None"),
 *                     @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *                     @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *                     @OA\Property(property="link",type="string",description="The link to the video stream",example="https://animethemes.moe/video/Bakemonogatari-OP1.webm"),
 *                 )),
 *             )),
 *         )),
 *         @OA\Property(property="resources",type="array",@OA\Items(
 *             @OA\Property(property="id",type="integer",description="Primary Key",example=1018),
 *             @OA\Property(property="link",type="string",description="The URL of the resource",example="https://myanimelist.net/anime/5081/"),
 *             @OA\Property(property="external_id",type="integer",description="The identifier used by the external site",example="5081"),
 *             @OA\Property(property="type",type="string",enum={"Official Website","Twitter","aniDB","AniList","Anime-Planet","Anime News Network","Kitsu","MyAnimeList","Wiki"},description="The site that we are linking to",example="MyAnimeList"),
 *             @OA\Property(property="as",type="string",description="Used to distinguish resources that map to the same artist or anime",example=""),
 *             @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *             @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *         )),
 *         @OA\Property(property="images",type="array",@OA\Items(
 *             @OA\Property(property="id",type="integer",description="Primary Key",example=1018),
 *             @OA\Property(property="path",type="string",description="The path of the Image in storage",example="anime/bakemonogatari.png"),
 *             @OA\Property(property="facet",type="string",enum={"Small Cover","Large Cover"},description="THe component of the page the image is intended for",example="Small Cover"),
 *             @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *             @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *         ))
 *     ))
 * )
 */
class SeriesResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'series';

    /**
     * Create a new resource instance.
     *
     * @param Series | MissingValue | null $series
     * @param QueryParser $parser
     * @return void
     */
    public function __construct(Series | MissingValue | null $series, QueryParser $parser)
    {
        parent::__construct($series, $parser);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->series_id),
            'name' => $this->when($this->isAllowedField('name'), $this->name),
            'slug' => $this->when($this->isAllowedField('slug'), $this->slug),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'anime' => AnimeCollection::make($this->whenLoaded('anime'), $this->parser),
        ];
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [
            'anime',
        ];
    }
}
