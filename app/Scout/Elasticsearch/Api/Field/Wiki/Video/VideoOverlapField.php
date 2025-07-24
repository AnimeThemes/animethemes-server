<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Video;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Models\Wiki\Video;
use App\Scout\Elasticsearch\Api\Field\EnumField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class VideoOverlapField extends EnumField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::ATTRIBUTE_OVERLAP, VideoOverlap::class);
    }
}
