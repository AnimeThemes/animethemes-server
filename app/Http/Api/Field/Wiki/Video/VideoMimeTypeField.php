<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Http\Api\Field\StringField;
use App\Models\Wiki\Video;

/**
 * Class VideoMimeTypeField.
 */
class VideoMimeTypeField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_MIMETYPE);
    }
}
