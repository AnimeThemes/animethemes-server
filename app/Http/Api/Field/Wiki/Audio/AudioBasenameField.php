<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Audio;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Audio;
use Illuminate\Http\Request;

/**
 * Class AudioBasenameField.
 */
class AudioBasenameField extends StringField implements CreatableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Audio::ATTRIBUTE_BASENAME);
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
            'max:192',
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
        $linkField = new AudioLinkField($this->schema);

        // The link field is dependent on this field to build the route.
        return parent::shouldSelect($query) || $linkField->shouldRender($query);
    }
}
