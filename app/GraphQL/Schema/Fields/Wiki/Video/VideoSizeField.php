<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Video;

use App\GraphQL\Schema\Fields\IntField;
use App\Models\Wiki\Video;

class VideoSizeField extends IntField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_SIZE);
    }

    public function description(): string
    {
        return 'The size of the file in storage in Bytes';
    }
}
