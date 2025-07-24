<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\Enums\Models\Wiki\VideoOverlap;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\Wiki\Video;

class VideoOverlapField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_OVERLAP, VideoOverlap::class);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The degree to which the sequence and episode content overlap';
    }
}
