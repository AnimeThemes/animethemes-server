<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
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
