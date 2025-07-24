<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Song\Performance;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PerformanceSongIdField extends Field implements CreatableField, SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Performance::ATTRIBUTE_SONG);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'integer',
            Rule::exists(Song::class, Song::ATTRIBUTE_ID),
        ];
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match song relation.
        return true;
    }
}
