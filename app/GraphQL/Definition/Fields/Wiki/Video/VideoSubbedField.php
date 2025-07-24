<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\GraphQL\Definition\Fields\BooleanField;
use App\Models\Wiki\Video;

class VideoSubbedField extends BooleanField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_SUBBED, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Does the video include subtitles of dialogue?';
    }
}
