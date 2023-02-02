<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Studio;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Pivots\Wiki\StudioResource;

/**
 * Class StudioAsField.
 */
class StudioAsField extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, StudioResource::ATTRIBUTE_AS);
    }
}
