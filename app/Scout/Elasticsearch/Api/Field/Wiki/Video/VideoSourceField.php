<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Video;

use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Video;
use App\Scout\Elasticsearch\Api\Field\EnumField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class VideoSourceField extends EnumField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::ATTRIBUTE_SOURCE, VideoSource::class);
    }
}
