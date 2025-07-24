<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video;

use App\Enums\Models\Wiki\VideoSource;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\Wiki\Video;

class VideoSourceField extends EnumField
{
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_SOURCE, VideoSource::class);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Where did this video come from?';
    }
}
