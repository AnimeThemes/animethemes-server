<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\ExternalProfile\ExternalEntry;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\List\External\ExternalEntry;
use App\Models\Wiki\Anime;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class ExternalEntryAnimeIdField.
 */
class ExternalEntryAnimeIdField extends Field implements CreatableField, SelectableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ExternalEntry::ATTRIBUTE_ANIME);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Query  $query
     * @param  Schema  $schema
     * @return bool
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match profile relation.
        return true;
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'integer',
            Rule::exists(Anime::class, Anime::ATTRIBUTE_ID),
        ];
    }
}
