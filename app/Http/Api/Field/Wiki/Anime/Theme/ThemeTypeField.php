<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime\AnimeTheme;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;

/**
 * Class ThemeTypeField.
 */
class ThemeTypeField extends EnumField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeTheme::ATTRIBUTE_TYPE, ThemeType::class);
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
            new EnumValue(ThemeType::class),
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
            new EnumValue(ThemeType::class),
        ];
    }
}
