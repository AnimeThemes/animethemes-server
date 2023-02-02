<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime;
use Illuminate\Http\Request;

/**
 * Class AnimeSynopsisField.
 */
class AnimeSynopsisField extends StringField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Anime::ATTRIBUTE_SYNOPSIS);
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
            'nullable',
            'string',
            'max:65535',
        ];
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
            'nullable',
            'string',
            'max:65535',
        ];
    }
}
