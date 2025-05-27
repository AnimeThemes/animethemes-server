<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\GraphQL\Definition\Fields\IntField;
use App\Models\Wiki\Video;

/**
 * Class VideoResolutionField.
 */
class VideoResolutionField extends IntField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_RESOLUTION);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The frame height of the file in storage';
    }
}
