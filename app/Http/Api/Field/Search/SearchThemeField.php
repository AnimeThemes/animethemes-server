<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Search;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;

class SearchThemeField extends Field
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ThemeCollection::$wrap);
    }
}
