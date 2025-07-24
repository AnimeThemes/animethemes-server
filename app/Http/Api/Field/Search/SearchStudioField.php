<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Search;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\Wiki\Collection\StudioCollection;

class SearchStudioField extends Field
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, StudioCollection::$wrap);
    }
}
