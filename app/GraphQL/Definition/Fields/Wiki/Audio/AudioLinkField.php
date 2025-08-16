<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Audio;

use App\GraphQL\Definition\Fields\Field;
use App\Models\Wiki\Audio;
use GraphQL\Type\Definition\Type;

class AudioLinkField extends Field
{
    public function __construct()
    {
        parent::__construct(Audio::ATTRIBUTE_LINK, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The URL to stream the file from storage';
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::string();
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}
