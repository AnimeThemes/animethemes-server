<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Base;

use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\IntField;
use App\Http\Resources\BaseResource;

/**
 * Class IdField.
 */
class IdField extends IntField
{
    /**
     * Create a new field instance.
     *
     * @param  string  $column
     */
    public function __construct(string $column)
    {
        parent::__construct(BaseResource::ATTRIBUTE_ID, $column);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Criteria|null  $criteria
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldSelect(?Criteria $criteria): bool
    {
        return true;
    }
}
