<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime;
use Illuminate\Http\Request;

class AnimeNameField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Anime::ATTRIBUTE_NAME);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'string',
            'max:192',
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'string',
            'max:192',
        ];
    }
}
