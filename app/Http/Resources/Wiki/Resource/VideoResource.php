<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\QueryParser;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\EntryCollection;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class VideoResource.
 */
class VideoResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'video';

    /**
     * Create a new resource instance.
     *
     * @param Video | MissingValue | null $video
     * @param QueryParser $parser
     * @return void
     */
    public function __construct(Video | MissingValue | null $video, QueryParser $parser)
    {
        parent::__construct($video, $parser);
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
            'source' => $this->when($this->isAllowedField('source'), strval(optional($this->source)->description)),
            'overlap' => $this->when($this->isAllowedField('overlap'), strval(optional($this->overlap)->description)),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'tags' => $this->when($this->isAllowedField('tags'), implode('', $this->tags)),
            'link' => $this->when($this->isAllowedField('link'), route('video.show', $this)),
            'entries' => EntryCollection::make($this->whenLoaded('entries'), $this->parser),
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
            'entries',
            'entries.theme',
            'entries.theme.anime',
        ];
    }
}
