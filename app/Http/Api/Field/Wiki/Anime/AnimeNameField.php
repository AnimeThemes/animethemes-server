<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime;

use App\Http\Api\Field\StringField;
use App\Models\Wiki\Anime;

/**
 * Class AnimeNameField.
 */
class AnimeNameField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_NAME);
    }
}
