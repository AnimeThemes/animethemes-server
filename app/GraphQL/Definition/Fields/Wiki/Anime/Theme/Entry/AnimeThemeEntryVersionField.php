<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\Theme\Entry;

use App\GraphQL\Definition\Fields\IntField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

/**
 * Class AnimeThemeEntryVersionField.
 */
class AnimeThemeEntryVersionField extends IntField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_VERSION);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The version number of the theme';
    }
}
