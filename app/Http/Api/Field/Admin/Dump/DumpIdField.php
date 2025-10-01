<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\Dump;

use App\Http\Api\Criteria\Include\Criteria;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Admin\Dump;

class DumpIdField extends IdField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Dump::ATTRIBUTE_ID);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $includeCriteria = $query->getIncludeCriteria($this->schema->type());
        $linkField = new DumpLinkField($this->schema);
        if ($this->schema->type() === $schema->type()
        && (! $includeCriteria instanceof Criteria || $includeCriteria->getPaths()->isEmpty())) {
            if (parent::shouldSelect($query, $schema)) {
                return true;
            }

            return $linkField->shouldRender($query);
        }

        return true;
    }
}
