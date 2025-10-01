<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThemeAnimeIdField extends Field implements CreatableField, SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeTheme::ATTRIBUTE_ANIME);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'integer',
            Rule::exists(Anime::class, Anime::ATTRIBUTE_ID),
        ];
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match anime relation.
        return true;
    }
}
