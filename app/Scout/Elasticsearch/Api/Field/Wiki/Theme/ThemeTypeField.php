<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Theme;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Theme;
use App\Scout\Elasticsearch\Api\Field\EnumField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class ThemeTypeField extends EnumField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Theme::ATTRIBUTE_TYPE, ThemeType::class);
    }
}
