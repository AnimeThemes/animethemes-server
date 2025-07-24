<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Audio;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Audio;
use Illuminate\Http\Request;

class AudioMimeTypeField extends StringField implements CreatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Audio::ATTRIBUTE_MIMETYPE);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'string',
            'max:192',
        ];
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
