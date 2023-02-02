<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Artist;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Artist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class ArtistSlugField.
 */
class ArtistSlugField extends StringField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Artist::ATTRIBUTE_SLUG);
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
            'max:192',
            'alpha_dash',
            Rule::unique(Artist::TABLE),
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
            'sometimes',
            'required',
            'max:192',
            'alpha_dash',
            Rule::unique(Artist::TABLE)->ignore($request->route('artist'), Artist::ATTRIBUTE_ID),
        ];
    }
}
