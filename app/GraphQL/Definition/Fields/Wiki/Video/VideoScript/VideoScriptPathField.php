<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video\VideoScript;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Video\VideoScript;

class VideoScriptPathField extends StringField
{
    public function __construct()
    {
        parent::__construct(VideoScript::ATTRIBUTE_PATH, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The path of the file in storage';
    }
}
