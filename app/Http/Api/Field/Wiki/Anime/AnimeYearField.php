<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime;

use App\Http\Api\Field\IntField;
use App\Models\Wiki\Anime;

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
