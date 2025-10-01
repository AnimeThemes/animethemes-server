<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ThemeTypeField extends EnumField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeTheme::ATTRIBUTE_TYPE, ThemeType::class);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            new Enum(ThemeType::class),
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(ThemeType::class),
        ];
    }
}
