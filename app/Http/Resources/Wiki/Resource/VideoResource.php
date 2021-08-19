<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Anime\Theme\Collection\EntryCollection;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class VideoResource.
 *
 * @mixin Video
 */
class VideoResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'video';

    /**
     * Create a new resource instance.
     *
     * @param Video | MissingValue | null $video
     * @param Query $query
     * @return void
     */
    public function __construct(Video | MissingValue | null $video, Query $query)
    {
        parent::__construct($video, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->video_id),
            'basename' => $this->when($this->isAllowedField('basename'), $this->basename),
            'filename' => $this->when($this->isAllowedField('filename'), $this->filename),
            'path' => $this->when($this->isAllowedField('path'), $this->path),
            'size' => $this->when($this->isAllowedField('size'), $this->size),
            'mimetype' => $this->when($this->isAllowedField('mimetype'), $this->mimetype),
            'resolution' => $this->when($this->isAllowedField('resolution'), $this->resolution),
            'nc' => $this->when($this->isAllowedField('nc'), $this->nc),
            'subbed' => $this->when($this->isAllowedField('subbed'), $this->subbed),
            'lyrics' => $this->when($this->isAllowedField('lyrics'), $this->lyrics),
            'uncen' => $this->when($this->isAllowedField('uncen'), $this->uncen),
            'source' => $this->when($this->isAllowedField('source'), $this->source?->description),
            'overlap' => $this->when($this->isAllowedField('overlap'), $this->overlap->description),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'tags' => $this->when($this->isAllowedField('tags'), implode('', $this->tags)),
            'link' => $this->when($this->isAllowedField('link'), route('video.show', $this)),
            'animethemeentries' => EntryCollection::make($this->whenLoaded('animethemeentries'), $this->query),
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
            'animethemeentries',
            'animethemeentries.animetheme',
            'animethemeentries.animetheme.anime',
        ];
    }
}
