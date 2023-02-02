<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Audio;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Wiki\Resource\AudioResource;

/**
 * Class AudioLinkField.
 */
class AudioLinkField extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AudioResource::ATTRIBUTE_LINK);
    }
}
