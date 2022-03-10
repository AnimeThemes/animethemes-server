<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme;

use App\Http\Api\Field\StringField;
use App\Models\Wiki\Anime\AnimeTheme;

/**
 * Class ThemeGroupField.
 */
class ThemeSlugField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeTheme::ATTRIBUTE_SLUG);
    }
}
