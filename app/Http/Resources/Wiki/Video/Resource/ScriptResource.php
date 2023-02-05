<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Video\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

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
     * Create a new resource instance.
     *
     * @param  VideoScript | MissingValue | null  $script
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(VideoScript|MissingValue|null $script, ReadQuery $query)
    {
        parent::__construct($script, $query);
    }

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
