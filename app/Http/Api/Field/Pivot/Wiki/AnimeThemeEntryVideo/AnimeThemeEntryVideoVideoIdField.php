<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\AnimeThemeEntryVideo;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Pivots\Wiki\AnimeThemeEntryVideo;

class AnimeThemeEntryVideoVideoIdField extends Field implements SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match entry relation.
        return true;
    }
}
