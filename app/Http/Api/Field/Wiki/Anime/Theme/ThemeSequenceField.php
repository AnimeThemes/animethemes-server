<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme;

use App\Http\Api\Field\IntField;
use App\Models\Wiki\Anime\AnimeTheme;

/**
 * Class ThemeSequenceField.
 */
class ThemeSequenceField extends IntField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeTheme::ATTRIBUTE_SEQUENCE);
    }
}
