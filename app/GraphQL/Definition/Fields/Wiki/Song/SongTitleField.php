<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Song;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Song;

class SongTitleField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Song::ATTRIBUTE_TITLE);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The name of the composition';
    }
}
