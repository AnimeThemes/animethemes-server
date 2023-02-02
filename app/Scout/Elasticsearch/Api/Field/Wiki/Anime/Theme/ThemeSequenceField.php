<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme;

use App\Models\Wiki\Anime\AnimeTheme;
use App\Scout\Elasticsearch\Api\Field\IntField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

/**
 * Class ThemeSequenceField.
 */
class ThemeSequenceField extends IntField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeTheme::ATTRIBUTE_SEQUENCE);
    }
}
