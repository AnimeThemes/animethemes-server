<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme\Entry;

use App\Http\Api\Field\Aggregate\CountField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

class EntryTrackCountField extends CountField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeThemeEntry::RELATION_TRACKS);
    }
}
