<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Search;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\List\Collection\PlaylistCollection;

class SearchPlaylistField extends Field
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, PlaylistCollection::$wrap);
    }
}
