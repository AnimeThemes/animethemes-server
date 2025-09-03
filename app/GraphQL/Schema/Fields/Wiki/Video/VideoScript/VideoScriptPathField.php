<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Video\VideoScript;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Video\VideoScript;

class VideoScriptPathField extends StringField
{
    public function __construct()
    {
        parent::__construct(VideoScript::ATTRIBUTE_PATH, nullable: false);
    }

    public function description(): string
    {
        return 'The path of the file in storage';
    }
}
