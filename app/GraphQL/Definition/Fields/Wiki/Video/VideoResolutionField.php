<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\GraphQL\Definition\Fields\IntField;
use App\Models\Wiki\Video;

class VideoResolutionField extends IntField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_RESOLUTION);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The frame height of the file in storage';
    }
}
