<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Morph\Imageable;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Pivots\Morph\Imageable;

class ImageableImageableIdField extends Field implements SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Imageable::ATTRIBUTE_IMAGEABLE_ID);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match anime relation.
        return true;
    }
}
