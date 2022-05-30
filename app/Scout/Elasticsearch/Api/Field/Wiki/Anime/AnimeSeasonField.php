<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Field\EnumField;

/**
 * Class AnimeSeasonField.
 */
class AnimeSeasonField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_SEASON, AnimeSeason::class);
    }
}
