<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Video\Resource;

use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Http\Request;

/**
 * Class ScriptResource.
 */
class ScriptResource extends BaseResource
{
    final public const ATTRIBUTE_LINK = 'link';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'videoscript';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[VideoScript::RELATION_VIDEO] = new VideoResource($this->whenLoaded(VideoScript::RELATION_VIDEO), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ScriptSchema();
    }
}
