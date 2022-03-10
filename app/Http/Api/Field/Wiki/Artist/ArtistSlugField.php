<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Artist;

use App\Http\Api\Field\StringField;
use App\Models\Wiki\Artist;

/**
 * Class ArtistSlugField.
 */
class ArtistSlugField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Artist::ATTRIBUTE_SLUG);
    }
}
