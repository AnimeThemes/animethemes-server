<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Audio;

use App\Http\Api\Field\CountField;
use App\Models\Wiki\Audio;

/**
 * Class AudioViewCountField.
 */
class AudioViewCountField extends CountField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Audio::RELATION_VIEWS);
    }
}
