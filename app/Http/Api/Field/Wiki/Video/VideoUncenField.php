<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Http\Api\Field\BooleanField;
use App\Models\Wiki\Video;

/**
 * Class VideoUncenField.
 */
class VideoUncenField extends BooleanField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_UNCEN);
    }
}
