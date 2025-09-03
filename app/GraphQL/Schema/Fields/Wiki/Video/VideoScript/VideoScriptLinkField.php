<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Video\VideoScript;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Video\VideoScript;

class VideoScriptLinkField extends StringField
{
    public function __construct()
    {
        parent::__construct(VideoScript::ATTRIBUTE_LINK, nullable: false);
    }

    public function description(): string
    {
        return 'The URL to download the file from storage';
    }
}
