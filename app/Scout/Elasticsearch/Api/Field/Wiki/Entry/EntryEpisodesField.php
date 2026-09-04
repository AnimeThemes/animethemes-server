<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Entry;

use App\Models\Wiki\Entry;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class EntryEpisodesField extends StringField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Entry::ATTRIBUTE_EPISODES);
    }
}
