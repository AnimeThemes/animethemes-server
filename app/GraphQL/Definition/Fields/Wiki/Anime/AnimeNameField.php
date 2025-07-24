<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Anime;

class AnimeNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_NAME, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The primary title of the anime';
    }
}
