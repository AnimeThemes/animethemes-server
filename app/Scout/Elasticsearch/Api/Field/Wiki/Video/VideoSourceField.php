<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Video;

use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Video;
use App\Scout\Elasticsearch\Api\Field\EnumField;

/**
 * Class VideoSourceField.
 */
class VideoSourceField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_SOURCE, VideoSource::class);
    }
}
