<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\IntField;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\Request;

/**
 * Class ThemeSequenceField.
 */
class ThemeSequenceField extends IntField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeTheme::ATTRIBUTE_SEQUENCE);
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
            'sometimes',
            'required',
            'integer',
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
            'integer',
        ];
    }
}
