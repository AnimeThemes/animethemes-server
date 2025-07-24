<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Theme\Entry;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Scout\Elasticsearch\Api\Field\IntField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class EntryVersionField extends IntField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeThemeEntry::ATTRIBUTE_VERSION);
    }
}
