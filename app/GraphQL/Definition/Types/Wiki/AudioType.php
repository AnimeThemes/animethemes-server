<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Wiki\Audio\AudioBasenameField;
use App\GraphQL\Definition\Fields\Wiki\Audio\AudioFilenameField;
use App\GraphQL\Definition\Fields\Wiki\Audio\AudioLinkField;
use App\GraphQL\Definition\Fields\Wiki\Audio\AudioMimetypeField;
use App\GraphQL\Definition\Fields\Wiki\Audio\AudioPathField;
use App\GraphQL\Definition\Fields\Wiki\Audio\AudioSizeField;
use App\GraphQL\Definition\Fields\Wiki\Audio\AudioViewsCountField;
use App\GraphQL\Definition\Relations\HasManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Wiki\Audio;

/**
 * Class AudioType.
 */
class AudioType extends EloquentType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents the audio track of a video.\n\nFor example, the audio Bakemonogatari-OP1.ogg represents the audio track of the Bakemonogatari-OP1.webm video.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new HasManyRelation(new VideoType(), Audio::RELATION_VIDEOS),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            new IdField(Audio::ATTRIBUTE_ID),
            new AudioBasenameField(),
            new AudioFilenameField(),
            new AudioMimetypeField(),
            new AudioSizeField(),
            new AudioPathField(),
            new AudioLinkField(),
            new AudioViewsCountField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
