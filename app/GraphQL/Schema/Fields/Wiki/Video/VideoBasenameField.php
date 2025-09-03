<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Video;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Video;

class VideoBasenameField extends StringField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_BASENAME, nullable: false);
    }

    public function description(): string
    {
        return 'The basename of the file in storage';
    }
}
