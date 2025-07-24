<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Field\EnumField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class AnimeSeasonField extends EnumField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Anime::ATTRIBUTE_SEASON, AnimeSeason::class);
    }
}
