<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video\VideoScript;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Video\VideoScript;

class VideoScriptLinkField extends StringField
{
    public function __construct()
    {
        parent::__construct(VideoScript::ATTRIBUTE_LINK, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The URL to download the file from storage';
    }
}
