<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Entry;

use App\Http\Api\Field\IntField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Entry;

class EntryTrackCountField extends IntField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Entry::ATTRIBUTE_TRACKS_COUNT);
    }
}
