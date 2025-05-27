<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\GraphQL\Definition\Fields\IntField;
use App\Models\Wiki\Video;

/**
 * Class VideoSizeField.
 */
class VideoSizeField extends IntField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_SIZE);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The size of the file in storage in Bytes';
    }
}
