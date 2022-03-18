<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Series;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Models\Wiki\Series;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class SeriesSlugField.
 */
class SeriesSlugField extends StringField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Series::ATTRIBUTE_SLUG);
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
            Rule::unique(Series::TABLE),
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
            Rule::unique(Series::TABLE)->ignore($request->route('series'), Series::ATTRIBUTE_ID),
        ];
    }
}
