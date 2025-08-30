<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\AnimeSeries;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Pivots\Wiki\AnimeSeries;

class AnimeSeriesAnimeIdField extends Field implements SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeSeries::ATTRIBUTE_ANIME);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match anime relation.
        return true;
    }
}
