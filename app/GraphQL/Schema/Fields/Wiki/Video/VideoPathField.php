<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Video;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Video;

class VideoPathField extends StringField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_PATH, nullable: false);
    }

    public function description(): string
    {
        return 'The path of the file in storage';
    }
}
