<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime;

use App\Http\Api\Field\Field;
use App\Pivots\AnimeResource;

/**
 * Class AnimeAsField.
 */
class AnimeAsField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeResource::ATTRIBUTE_AS);
    }
}
