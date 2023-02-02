<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Field\EnumField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

/**
 * Class AnimeSeasonField.
 */
class AnimeSeasonField extends EnumField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Anime::ATTRIBUTE_SEASON, AnimeSeason::class);
    }
}
