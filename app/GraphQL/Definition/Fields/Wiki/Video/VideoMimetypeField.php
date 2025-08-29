<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Video;

class VideoMimetypeField extends StringField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_MIMETYPE);
    }

    public function description(): string
    {
        return 'The media type of the file in storage';
    }
}
