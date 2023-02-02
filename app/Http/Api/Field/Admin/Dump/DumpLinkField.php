<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\Dump;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Admin\Resource\DumpResource;

/**
 * Class DumpLinkField.
 */
class DumpLinkField extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema,DumpResource::ATTRIBUTE_LINK);
    }
}
