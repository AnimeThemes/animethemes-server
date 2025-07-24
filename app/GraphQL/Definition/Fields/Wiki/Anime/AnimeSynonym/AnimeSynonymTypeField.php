<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\AnimeSynonym;

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\Wiki\Anime\AnimeSynonym;

class AnimeSynonymTypeField extends EnumField
{
    public function __construct()
    {
        parent::__construct(AnimeSynonym::ATTRIBUTE_TYPE, AnimeSynonymType::class);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The type of the synonym';
    }
}
