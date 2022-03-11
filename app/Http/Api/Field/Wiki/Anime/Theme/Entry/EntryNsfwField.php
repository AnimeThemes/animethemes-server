<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme\Entry;

use App\Http\Api\Field\BooleanField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

/**
 * Class EntryNsfwField.
 */
class EntryNsfwField extends BooleanField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::ATTRIBUTE_NSFW);
    }
}
