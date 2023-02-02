<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme\Entry;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Scout\Elasticsearch\Api\Field\BooleanField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

/**
 * Class EntrySpoilerField.
 */
class EntrySpoilerField extends BooleanField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeThemeEntry::ATTRIBUTE_SPOILER);
    }
}
