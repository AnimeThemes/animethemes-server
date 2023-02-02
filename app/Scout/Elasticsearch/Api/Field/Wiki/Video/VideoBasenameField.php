<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Video;

use App\Models\Wiki\Video;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

/**
 * Class VideoBasenameField.
 */
class VideoBasenameField extends StringField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::ATTRIBUTE_BASENAME);
    }
}
