<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\Theme\Entry;

use App\GraphQL\Definition\Fields\IntField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

class AnimeThemeEntryVersionField extends IntField
{
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_VERSION);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The version number of the theme';
    }
}
