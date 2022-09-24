<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Video\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\BaseModel;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class ScriptResource.
 *
 * @mixin VideoScript
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(VideoScript::ATTRIBUTE_PATH)) {
            $result[VideoScript::ATTRIBUTE_PATH] = $this->path;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        if ($this->isAllowedField(ScriptResource::ATTRIBUTE_LINK)) {
            $result[ScriptResource::ATTRIBUTE_LINK] = route('videoscript.show', $this);
        }

        $result[VideoScript::RELATION_VIDEO] = new VideoResource($this->whenLoaded(VideoScript::RELATION_VIDEO), $this->query);

        return $result;
    }
}
