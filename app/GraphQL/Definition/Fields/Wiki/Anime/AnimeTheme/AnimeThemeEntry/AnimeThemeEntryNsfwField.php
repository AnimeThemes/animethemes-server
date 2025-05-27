<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeTheme\AnimeThemeEntry;

use App\GraphQL\Definition\Fields\BooleanField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

/**
 * Class AnimeThemeEntryNsfwField.
 */
class AnimeThemeEntryNsfwField extends BooleanField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_NSFW, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Is not safe for work content included?';
    }
}
