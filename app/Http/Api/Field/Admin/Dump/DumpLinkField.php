<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\Dump;

use App\Http\Api\Field\Field;
use App\Http\Resources\Admin\Resource\DumpResource;

/**
 * Class DumpLinkField.
 */
class DumpLinkField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(DumpResource::ATTRIBUTE_LINK);
    }
}
