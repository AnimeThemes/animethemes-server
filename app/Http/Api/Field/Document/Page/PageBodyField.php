<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Document\Page;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Models\Document\Page;
use Illuminate\Http\Request;

/**
 * Class PageBodyField.
 */
class PageBodyField extends Field implements CreatableField, SelectableField, UpdatableField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Page::ATTRIBUTE_BODY);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'string',
            'max:16777215',
        ];
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  ReadQuery  $query
     * @return bool
     */
    public function shouldSelect(ReadQuery $query): bool
    {
        $criteria = $query->getFieldCriteria($this->schema->type());

        // TODO: Only return this attribute if specified due to potential size.
        return $criteria === null || $criteria->isAllowedField($this->getKey());
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'string',
            'max:16777215',
        ];
    }
}
