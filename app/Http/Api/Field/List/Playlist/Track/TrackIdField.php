<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist\Track;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\List\Playlist\PlaylistTrack;

class TrackIdField extends Field implements SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, PlaylistTrack::ATTRIBUTE_ID);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // We can only exclude ID fields for top-level models that are not including related resources.
        $includeCriteria = $query->getIncludeCriteria($this->schema->type());
        if (
            $this->schema->type() === $schema->type()
            && ($includeCriteria === null || $includeCriteria->getPaths()->isEmpty())
        ) {
            $criteria = $query->getFieldCriteria($this->schema->type());

            return $criteria === null || $criteria->isAllowedField($this->getKey());
        }

        return true;
    }
}
