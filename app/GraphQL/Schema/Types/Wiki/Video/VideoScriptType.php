<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki\Video;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoScript\VideoScriptLinkField;
use App\GraphQL\Schema\Fields\Wiki\Video\VideoScript\VideoScriptPathField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Wiki\VideoType;
use App\GraphQL\Schema\Relations\HasOneRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\Models\Wiki\Video\VideoScript;

class VideoScriptType extends EloquentType
{
    public function description(): string
    {
        return "Represents an encoding script used to produce a video.\n\nFor example, the 2009/Summer/Bakemonogatari-OP1.txt video script represents the encoding script of the Bakemonogatari-OP1.webm video.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new HasOneRelation(new VideoType(), VideoScript::RELATION_VIDEO)
                ->notNullable(),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(VideoScript::ATTRIBUTE_ID, VideoScript::class),
            new VideoScriptPathField(),
            new VideoScriptLinkField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
