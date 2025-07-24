<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\Theme\Entry;

use App\GraphQL\Definition\Fields\BooleanField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

class AnimeThemeEntryNsfwField extends BooleanField
{
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_NSFW, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Is not safe for work content included?';
    }
}
