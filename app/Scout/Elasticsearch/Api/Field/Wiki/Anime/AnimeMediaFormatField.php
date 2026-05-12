<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeFormat;
use App\Models\Wiki\Anime;
use App\Scout\Elasticsearch\Api\Field\EnumField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class AnimeMediaFormatField extends EnumField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, 'media_format', AnimeFormat::class, Anime::ATTRIBUTE_FORMAT);
    }
}
