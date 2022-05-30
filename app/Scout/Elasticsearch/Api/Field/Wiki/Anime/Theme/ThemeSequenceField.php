<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme;

use App\Models\Wiki\Anime\AnimeTheme;
use App\Scout\Elasticsearch\Api\Field\IntField;

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
