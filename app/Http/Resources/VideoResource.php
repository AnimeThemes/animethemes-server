<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="Video",
 *     description="Video Resource",
 *     type="object"
 * )
 */
class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @OA\Property(property="id",type="integer",description="Primary Key",example=1850)
     * @OA\Property(property="basename",type="string",description="The basename of the Video",example="Bakemonogatari-OP1.webm")
     * @OA\Property(property="filename",type="string",description="The filename of the Video",example="Bakemonogatari-OP1")
     * @OA\Property(property="path",type="string",description="The path of the Video in storage",example="2009/Summer/Bakemonogatari-OP1.webm")
     * @OA\Property(property="resolution",type="integer",description="Frame height of the video",example=1080)
     * @OA\Property(property="nc",type="bool",description="Is the video creditless?",example="true")
     * @OA\Property(property="subbed",type="bool",description="Does the video include subtitles of dialogue?",example="false")
     * @OA\Property(property="lyrics",type="bool",description="Does the video include subtitles for song lyrics?",example="true")
     * @OA\Property(property="uncen",type="bool",description="Is the video an uncensored version of a censored sequence?",example="true")
     * @OA\Property(property="source",type="string",description="Where did this video come from?",example="BD")
     * @OA\Property(property="overlap",type="string",description="The degree to which the sequence and episode content overlap",example="Trans")
     * @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:30:43.000000Z")
     * @OA\Property(property="link",type="string",description="The link to the video stream",example="https://animethemes.moe/video/Bakemonogatari-OP1.webm")
     * @OA\Property(property="entries",type="object",ref="#/components/schemas/EntryResource")
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->video_id,
            'basename' => $this->basename,
            'filename' => $this->filename,
            'path' => $this->path,
            'resolution' => $this->resolution,
            'nc' => $this->nc,
            'subbed' => $this->subbed,
            'lyrics' => $this->lyrics,
            'uncen' => $this->uncen,
            'source' => $this->source->description,
            'overlap' => $this->overlap->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'link' => route('video.show', $this),
            'entries' => EntryResource::collection($this->whenLoaded('entries'))
        ];
    }
}
