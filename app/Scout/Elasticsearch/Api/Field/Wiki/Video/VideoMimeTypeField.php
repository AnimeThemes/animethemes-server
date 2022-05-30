<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Video;

use App\Models\Wiki\Video;
use App\Scout\Elasticsearch\Api\Field\StringField;

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
