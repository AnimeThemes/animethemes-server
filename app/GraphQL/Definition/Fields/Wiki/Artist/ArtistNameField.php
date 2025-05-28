<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Artist;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Artist;

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
        parent::__construct(Artist::ATTRIBUTE_NAME, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The primary title of the artist';
    }
}
