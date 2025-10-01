<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme\Entry;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\IntField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Http\Request;

class EntryVersionField extends IntField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeThemeEntry::ATTRIBUTE_VERSION);
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
