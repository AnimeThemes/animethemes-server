<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Song;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThemeSongIdField extends Field implements CreatableField, SelectableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeTheme::ATTRIBUTE_SONG);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            Rule::exists(Song::class, Song::ATTRIBUTE_ID),
        ];
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match song relation.
        return true;
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            Rule::exists(Song::class, Song::ATTRIBUTE_ID),
        ];
    }
}
