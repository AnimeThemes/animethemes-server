<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Video\VideoScript;

use App\GraphQL\Schema\Fields\Field;
use App\Models\Wiki\Video\VideoScript;
use GraphQL\Type\Definition\Type;

class VideoScriptLinkField extends Field
{
    public function __construct()
    {
        parent::__construct(VideoScript::ATTRIBUTE_LINK, nullable: false);
    }

    public function description(): string
    {
        return 'The URL to download the file from storage';
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
