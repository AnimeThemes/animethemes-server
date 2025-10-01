<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Criteria\Include\Criteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\List\Playlist;

class PlaylistIdField extends Field implements SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Playlist::ATTRIBUTE_ID);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // We can only exclude ID fields for top-level models that are not including related resources.
        $includeCriteria = $query->getIncludeCriteria($this->schema->type());
        if (
            $this->schema->type() === $schema->type()
            && (! $includeCriteria instanceof Criteria || $includeCriteria->getPaths()->isEmpty())
        ) {
            $criteria = $query->getFieldCriteria($this->schema->type());

            return ! $criteria instanceof \App\Http\Api\Criteria\Field\Criteria || $criteria->isAllowedField($this->getKey());
        }

        return true;
    }
}
