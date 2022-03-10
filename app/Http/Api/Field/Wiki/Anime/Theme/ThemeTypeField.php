<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme;

use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\EnumField;
use App\Models\Wiki\Anime\AnimeTheme;

/**
 * Class ThemeTypeField.
 */
class ThemeTypeField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeTheme::ATTRIBUTE_TYPE, ThemeType::class);
    }
}
