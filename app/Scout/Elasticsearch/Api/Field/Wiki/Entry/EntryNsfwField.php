<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Entry;

use App\Models\Wiki\Entry;
use App\Scout\Elasticsearch\Api\Field\BooleanField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class EntryNsfwField extends BooleanField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Entry::ATTRIBUTE_NSFW);
    }
}
