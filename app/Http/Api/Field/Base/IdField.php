<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Base;

use App\Http\Api\Field\IntField;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;

/**
 * Class IdField.
 */
class IdField extends IntField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     * @param  string  $column
     */
    public function __construct(Schema $schema, string $column)
    {
        parent::__construct($schema, BaseResource::ATTRIBUTE_ID, $column);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  ReadQuery  $query
     * @return bool
     */
    public function shouldSelect(ReadQuery $query): bool
    {

        // TODO: if field criteria or allowed includes is not empty
        return true;
    }
}
