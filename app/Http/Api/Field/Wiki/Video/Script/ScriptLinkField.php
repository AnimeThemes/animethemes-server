<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video\Script;

use App\Http\Api\Field\Field;
use App\Http\Resources\Wiki\Video\Resource\ScriptResource;

/**
 * Class ScriptLinkField.
 */
class ScriptLinkField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ScriptResource::ATTRIBUTE_LINK);
    }
}
