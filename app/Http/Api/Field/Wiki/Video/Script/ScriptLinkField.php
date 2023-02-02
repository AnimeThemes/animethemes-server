<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video\Script;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Wiki\Video\Resource\ScriptResource;

/**
 * Class ScriptLinkField.
 */
class ScriptLinkField extends Field
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ScriptResource::ATTRIBUTE_LINK);
    }
}
