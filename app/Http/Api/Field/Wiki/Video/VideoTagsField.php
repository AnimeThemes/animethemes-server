<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Video;

/**
 * Class VideoTagsField.
 */
class VideoTagsField extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::ATTRIBUTE_TAGS);
    }
}
