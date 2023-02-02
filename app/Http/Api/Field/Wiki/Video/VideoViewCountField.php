<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Http\Api\Field\CountField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Video;

/**
 * Class VideoViewCountField.
 */
class VideoViewCountField extends CountField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::RELATION_VIEWS);
    }
}
