<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Artist;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Artist;

class ArtistInformationField extends StringField
{
    public function __construct()
    {
        parent::__construct(Artist::ATTRIBUTE_INFORMATION);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The brief information of the resource';
    }
}
