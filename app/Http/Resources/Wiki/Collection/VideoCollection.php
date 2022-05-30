<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;

/**
 * Class VideoCollection.
 */
class VideoCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'videos';

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
        return $this->collection->map(fn (Video $video) => new VideoResource($video, $this->query))->all();
    }
}
