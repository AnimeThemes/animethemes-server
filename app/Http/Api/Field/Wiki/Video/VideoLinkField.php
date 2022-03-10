<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Http\Api\Field\Field;
use App\Http\Resources\Wiki\Resource\VideoResource;

/**
 * Class VideoLinkField.
 */
class VideoLinkField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(VideoResource::ATTRIBUTE_LINK);
    }
}
