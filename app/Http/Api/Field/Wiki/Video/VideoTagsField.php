<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Http\Api\Field\Field;
use App\Models\Wiki\Video;

/**
 * Class VideoTagsField.
 */
class VideoTagsField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_TAGS);
    }
}
