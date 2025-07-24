<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\Theme\Entry;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

class AnimeThemeEntryEpisodesField extends StringField
{
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_EPISODES);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The episodes that the theme is used for';
    }
}
