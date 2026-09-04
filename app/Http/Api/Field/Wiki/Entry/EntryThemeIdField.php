<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Entry;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EntryThemeIdField extends Field implements CreatableField, SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Entry::ATTRIBUTE_THEME);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'integer',
            Rule::exists(Theme::class, Theme::ATTRIBUTE_ID),
        ];
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match theme relation.
        return true;
    }
}
