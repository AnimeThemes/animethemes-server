<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Audio;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Field\StringField;
use App\Models\Wiki\Audio;
use Illuminate\Http\Request;

/**
 * Class AudioPathField.
 */
class AudioPathField extends StringField implements CreatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Audio::ATTRIBUTE_PATH);
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
}
