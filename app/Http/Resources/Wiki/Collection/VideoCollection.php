<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\SearchableCollection;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;

/**
 * Class VideoCollection.
 */
class VideoCollection extends SearchableCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'videos';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Video::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(fn (Video $video) => VideoResource::make($video, $this->query))->all();
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new VideoSchema();
    }
}
