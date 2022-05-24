<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Artist;

use App\Models\Wiki\Artist;
use App\Scout\Elasticsearch\Api\Field\StringField;

/**
 * Class ArtistNameField.
 */
class ArtistNameField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Artist::ATTRIBUTE_NAME);
    }
}
