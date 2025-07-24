<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Video;

use App\Models\Wiki\Video;
use App\Scout\Elasticsearch\Api\Field\BooleanField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class VideoLyricsField extends BooleanField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::ATTRIBUTE_LYRICS);
    }
}
