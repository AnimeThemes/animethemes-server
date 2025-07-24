<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSynonym;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Anime\AnimeSynonym;

class AnimeSynonymTextField extends StringField
{
    public function __construct()
    {
        parent::__construct(AnimeSynonym::ATTRIBUTE_TEXT, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The alternate title or common abbreviations';
    }
}
