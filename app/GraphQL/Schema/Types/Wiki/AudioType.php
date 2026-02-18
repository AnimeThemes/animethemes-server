<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\HasManyRelation;
use App\GraphQL\Schema\Fields\Wiki\Audio\AudioBasenameField;
use App\GraphQL\Schema\Fields\Wiki\Audio\AudioFilenameField;
use App\GraphQL\Schema\Fields\Wiki\Audio\AudioLinkField;
use App\GraphQL\Schema\Fields\Wiki\Audio\AudioMimetypeField;
use App\GraphQL\Schema\Fields\Wiki\Audio\AudioPathField;
use App\GraphQL\Schema\Fields\Wiki\Audio\AudioSizeField;
use App\GraphQL\Schema\Types\EloquentType;
use App\Models\Wiki\Audio;

class AudioType extends EloquentType
{
    public function description(): string
    {
        return "Represents the audio track of a video.\n\nFor example, the audio Bakemonogatari-OP1.ogg represents the audio track of the Bakemonogatari-OP1.webm video.";
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(Audio::ATTRIBUTE_ID, Audio::class),
            new AudioBasenameField(),
            new AudioFilenameField(),
            new AudioMimetypeField(),
            new AudioSizeField(),
            new AudioPathField(),
            new AudioLinkField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),

            new HasManyRelation(new VideoType(), Audio::RELATION_VIDEOS),
        ];
    }
}
