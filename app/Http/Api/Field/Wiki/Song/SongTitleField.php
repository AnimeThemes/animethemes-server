<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Song;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Song;
use Illuminate\Http\Request;

class SongTitleField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Song::ATTRIBUTE_TITLE);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
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
