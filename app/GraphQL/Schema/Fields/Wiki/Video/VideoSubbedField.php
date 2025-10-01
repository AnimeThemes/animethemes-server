<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Video;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\BooleanField;
use App\Models\Wiki\Video;

class VideoSubbedField extends BooleanField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_SUBBED, nullable: false);
    }

    public function description(): string
    {
        return 'Does the video include subtitles of dialogue?';
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            'boolean',
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            'boolean',
        ];
    }
}
