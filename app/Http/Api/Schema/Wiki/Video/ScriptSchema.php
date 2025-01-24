<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki\Video;

use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Video\Script\ScriptIdField;
use App\Http\Api\Field\Wiki\Video\Script\ScriptLinkField;
use App\Http\Api\Field\Wiki\Video\Script\ScriptPathField;
use App\Http\Api\Field\Wiki\Video\Script\ScriptVideoIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\Wiki\Video\Resource\ScriptResource;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class ScriptSchema.
 */
class ScriptSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ScriptResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    protected function finalAllowedIncludes(): array
    {
        return [
            new AllowedInclude(new VideoSchema(), VideoScript::RELATION_VIDEO),
        ];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new ScriptIdField($this),
                new ScriptPathField($this),
                new ScriptLinkField($this),
                new ScriptVideoIdField($this),
            ],
        );
    }
}
