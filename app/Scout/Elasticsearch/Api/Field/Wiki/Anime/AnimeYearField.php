<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime;

use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Field\IntField;

/**
 * Class AnimeYearField.
 */
class AnimeYearField extends IntField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_YEAR);
    }
}
