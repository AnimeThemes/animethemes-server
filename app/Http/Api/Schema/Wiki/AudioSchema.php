<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Audio\AudioBasenameField;
use App\Http\Api\Field\Wiki\Audio\AudioFilenameField;
use App\Http\Api\Field\Wiki\Audio\AudioLinkField;
use App\Http\Api\Field\Wiki\Audio\AudioMimeTypeField;
use App\Http\Api\Field\Wiki\Audio\AudioPathField;
use App\Http\Api\Field\Wiki\Audio\AudioSizeField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Wiki\Resource\AudioJsonResource;
use App\Models\Wiki\Audio;

class AudioSchema extends EloquentSchema
{
    public function type(): string
    {
        return AudioJsonResource::$wrap;
    }

    /**
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new VideoSchema(), Audio::RELATION_VIDEOS),
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
                new IdField($this, Audio::ATTRIBUTE_ID),
                new AudioBasenameField($this),
                new AudioFilenameField($this),
                new AudioMimeTypeField($this),
                new AudioPathField($this),
                new AudioSizeField($this),
                new AudioLinkField($this),
            ],
        );
    }
}
