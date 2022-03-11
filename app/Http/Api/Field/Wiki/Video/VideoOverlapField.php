<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Http\Api\Field\EnumField;
use App\Models\Wiki\Video;

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
