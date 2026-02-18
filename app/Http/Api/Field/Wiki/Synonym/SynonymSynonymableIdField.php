<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Synonym;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SynonymSynonymableIdField extends Field implements CreatableField, SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Synonym::ATTRIBUTE_SYNONYMABLE_ID);
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
        // Needed to match synonymable relation.
        return true;
    }
}
