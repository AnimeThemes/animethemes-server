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
use App\Http\Api\Field\Wiki\Audio\AudioViewCountField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Wiki\Resource\AudioResource;
use App\Models\Wiki\Audio;

/**
 * Class AudioSchema.
 */
class AudioSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return AudioResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    protected function finalAllowedIncludes(): array
    {
        return [
            new AllowedInclude(new VideoSchema(), Audio::RELATION_VIDEOS),
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
                new IdField($this, Audio::ATTRIBUTE_ID),
                new AudioBasenameField($this),
                new AudioFilenameField($this),
                new AudioMimeTypeField($this),
                new AudioPathField($this),
                new AudioSizeField($this),
                new AudioLinkField($this),
                new AudioViewCountField($this),
            ],
        );
    }
}
