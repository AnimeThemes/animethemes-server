<?php

namespace App\Http\Resources;

/**
 * @OA\Schema(
 *     title="Entry",
 *     description="Entry Resource",
 *     type="object",
 *     @OA\Property(property="id",type="integer",description="Primary Key",example=3518),
 *     @OA\Property(property="version",type="integer",description="The Version number of the Theme",example=""),
 *     @OA\Property(property="episodes",type="string",description="The range(s) of episodes that the theme entry is used",example="1-2, 12"),
 *     @OA\Property(property="nsfw",type="bool",description="Does the entry include Not Safe For Work content?",example="false"),
 *     @OA\Property(property="spoiler",type="bool",description="Does the entry include content that spoils the show?",example="false"),
 *     @OA\Property(property="notes",type="string",description="Any additional information not included in other fields that may be useful",example=""),
 *     @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *     @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *     @OA\Property(property="anime",type="object",
 *         @OA\Property(property="id",type="integer",description="Primary Key",example=197),
 *         @OA\Property(property="name",type="string",description="The Primary Title of the Anime",example="Bakemonogatari"),
 *         @OA\Property(property="slug",type="string",description="URL Slug & Model Route Key",example="bakemonogatari"),
 *         @OA\Property(property="year",type="integer",description="The Year in which the Anime Premiered",example=2009),
 *         @OA\Property(property="season",type="string",enum={"Winter","Spring","Summer","Fall"},description="The Season in which the Anime Premiered",example="Summer"),
 *         @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *         @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *     ),
 *     @OA\Property(property="theme",type="object",
 *         @OA\Property(property="id",type="integer",description="Primary Key",example=3102),
 *         @OA\Property(property="type",type="string",enum={"OP","ED"},description="Is this an OP or an ED?",example="OP"),
 *         @OA\Property(property="sequence",type="integer",description="Numeric ordering of theme",example="1"),
 *         @OA\Property(property="group",type="string",description="For separating sequences belonging to dubs, rebroadcasts, remasters, etc",example=""),
 *         @OA\Property(property="slug",type="bool",description="URL Slug",example="OP1"),
 *         @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *         @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:43:02.000000Z"),
 *     ),
 *     @OA\Property(property="videos",type="array",@OA\Items(
 *         @OA\Property(property="id",type="integer",description="Primary Key",example=2615),
 *         @OA\Property(property="basename",type="string",description="The basename of the Video",example="Bakemonogatari-OP1.webm"),
 *         @OA\Property(property="filename",type="string",description="The filename of the Video",example="Bakemonogatari-OP1"),
 *         @OA\Property(property="path",type="string",description="The path of the Video in storage",example="2009/Summer/Bakemonogatari-OP1.webm"),
 *         @OA\Property(property="resolution",type="integer",description="Frame height of the video",example=1080),
 *         @OA\Property(property="nc",type="bool",description="Is the video creditless?",example="true"),
 *         @OA\Property(property="subbed",type="bool",description="Does the video include subtitles of dialogue?",example="false"),
 *         @OA\Property(property="lyrics",type="bool",description="Does the video include subtitles for song lyrics?",example="false"),
 *         @OA\Property(property="uncen",type="bool",description="Is the video an uncensored version of a censored sequence?",example="false"),
 *         @OA\Property(property="source",type="string",enum={"WEB","RAW","BD","DVD","VHS"},description="Where did this video come from?",example="BD"),
 *         @OA\Property(property="overlap",type="string",enum={"None","Transition","Over"},description="The degree to which the sequence and episode content overlap",example="None"),
 *         @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *         @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *         @OA\Property(property="link",type="string",description="The link to the video stream",example="https://animethemes.moe/video/Bakemonogatari-OP1.webm"),
 *     )),
 * )
 */
class EntryResource extends BaseResource
{
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
    public static $resourceType = 'entry';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->entry_id),
            'version' => $this->when($this->isAllowedField('version'), is_null($this->version) ? '' : $this->version),
            'episodes' => $this->when($this->isAllowedField('episodes'), $this->episodes),
            'nsfw' => $this->when($this->isAllowedField('nsfw'), $this->nsfw),
            'spoiler' => $this->when($this->isAllowedField('spoiler'), $this->spoiler),
            'notes' => $this->when($this->isAllowedField('notes'), $this->notes),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'anime' => AnimeResource::make($this->whenLoaded('anime'), $this->parser),
            'theme' => ThemeResource::make($this->whenLoaded('theme'), $this->parser),
            'videos' => VideoCollection::make($this->whenLoaded('videos'), $this->parser),
        ];
    }
}