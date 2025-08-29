<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Morph\Resourceable;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Pivots\Morph\Resourceable;

class ResourceableResourceIdField extends Field implements SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Resourceable::ATTRIBUTE_RESOURCE);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match resource relation.
        return true;
    }
}
