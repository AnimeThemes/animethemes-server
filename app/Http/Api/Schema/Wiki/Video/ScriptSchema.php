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
use Illuminate\Database\Eloquent\Model;

class ScriptSchema extends EloquentSchema
{
    public function type(): string
    {
        return ScriptResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new VideoSchema(), VideoScript::RELATION_VIDEO),
        ]);
    }

    /**
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

    /**
     * Get the model of the schema.
     */
    public function model(): VideoScript
    {
        return new VideoScript();
    }
}
