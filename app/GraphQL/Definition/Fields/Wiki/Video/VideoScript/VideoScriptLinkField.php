<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Video\VideoScript;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Video\VideoScript;

class VideoScriptLinkField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(VideoScript::ATTRIBUTE_LINK, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The URL to download the file from storage';
    }
}
