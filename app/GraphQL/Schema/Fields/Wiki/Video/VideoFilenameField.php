<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Video;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Video;

class VideoFilenameField extends StringField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_FILENAME, nullable: false);
    }

    public function description(): string
    {
        return 'The filename of the file in storage';
    }
}
