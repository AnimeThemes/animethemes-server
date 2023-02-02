<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme\Entry;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class EntryThemeIdField.
 */
class EntryThemeIdField extends Field implements CreatableField, SelectableField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeThemeEntry::ATTRIBUTE_THEME);
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
            'integer',
            Rule::exists(AnimeTheme::TABLE, AnimeTheme::ATTRIBUTE_ID),
        ];
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  ReadQuery  $query
     * @return bool
     */
    public function shouldSelect(ReadQuery $query): bool
    {
        // Needed to match theme relation.
        return true;
    }
}
