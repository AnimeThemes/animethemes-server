<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Video;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Models\Wiki\Video;
use App\Scout\Elasticsearch\Api\Field\EnumField;

/**
 * Class VideoOverlapField.
 */
class VideoOverlapField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_OVERLAP, VideoOverlap::class);
    }
}
