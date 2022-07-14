<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Audio;

use App\Http\Api\Field\Field;
use App\Http\Resources\Wiki\Resource\AudioResource;

/**
 * Class AudioLinkField.
 */
class AudioLinkField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AudioResource::ATTRIBUTE_LINK);
    }
}
