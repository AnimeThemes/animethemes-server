<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeTheme\AnimeThemeEntry;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

/**
 * Class AnimeThemeEntryNotesField.
 */
class AnimeThemeEntryNotesField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_NOTES);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Any additional information for this sequence';
    }
}
