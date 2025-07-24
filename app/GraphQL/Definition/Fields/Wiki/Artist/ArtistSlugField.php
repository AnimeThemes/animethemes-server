<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Artist;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Artist;

class ArtistSlugField extends StringField
{
    public function __construct()
    {
        parent::__construct(Artist::ATTRIBUTE_SLUG, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The URL slug & route key of the resource';
    }
}
