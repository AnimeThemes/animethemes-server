<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Theme;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class ThemeAnimeIdField.
 */
class ThemeAnimeIdField extends Field implements CreatableField, SelectableField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeTheme::ATTRIBUTE_ANIME);
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
            Rule::exists(Anime::TABLE, Anime::ATTRIBUTE_ID),
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
        // Needed to match anime relation.
        return true;
    }
}
