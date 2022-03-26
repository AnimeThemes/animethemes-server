<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Field\StringField;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;

/**
 * Class VideoPathField.
 */
class VideoPathField extends StringField implements CreatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Video::ATTRIBUTE_PATH);
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
