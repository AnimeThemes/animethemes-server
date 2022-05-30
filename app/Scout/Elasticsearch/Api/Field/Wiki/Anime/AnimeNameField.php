<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime;

use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Field\StringField;

/**
 * Class AnimeNameField.
 */
class AnimeNameField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_NAME);
    }
}
