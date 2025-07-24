<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Base;

use App\Constants\ModelConstants;
use App\Http\Api\Field\DateField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;

class DeletedAtField extends DateField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ModelConstants::ATTRIBUTE_DELETED_AT);
    }

    /**
     * Determine if the field should be displayed to the user.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldRender(Query $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria !== null && $criteria->isAllowedField($this->getKey());
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        return $criteria !== null && $criteria->isAllowedField($this->getKey());
    }
}
