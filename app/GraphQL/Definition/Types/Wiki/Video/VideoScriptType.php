<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Video;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoScript\VideoScriptLinkField;
use App\GraphQL\Definition\Fields\Wiki\Video\VideoScript\VideoScriptPathField;
use App\GraphQL\Definition\Relations\HasOneRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Wiki\VideoType;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class VideoScriptType.
 */
class VideoScriptType extends EloquentType implements HasFields, HasRelations
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
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
            new HasOneRelation(new VideoType(), VideoScript::RELATION_VIDEO, nullable: false),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new IdField(VideoScript::ATTRIBUTE_ID),
            new VideoScriptPathField(),
            new VideoScriptLinkField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
