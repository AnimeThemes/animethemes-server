<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Audio;

use App\GraphQL\Schema\Fields\Field;
use App\Models\Wiki\Audio;
use GraphQL\Type\Definition\Type;

class AudioLinkField extends Field
{
    public function __construct()
    {
        parent::__construct(Audio::ATTRIBUTE_LINK, nullable: false);
    }

    public function description(): string
    {
        return 'The URL to stream the file from storage';
    }

    public function baseType(): Type
    {
        return Type::string();
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
