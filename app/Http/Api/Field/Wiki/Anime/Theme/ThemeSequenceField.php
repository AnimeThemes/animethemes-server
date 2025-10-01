<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\IntField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\Request;

class ThemeSequenceField extends IntField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeTheme::ATTRIBUTE_SEQUENCE);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            'min:0',
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            'min:0',
        ];
    }
}
