<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\AnimeSeries;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class AnimeSeriesSeriesIdField.
 */
class AnimeSeriesSeriesIdField extends Field implements CreatableField, SelectableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeSeries::ATTRIBUTE_SERIES);
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
            Rule::exists(Series::TABLE, Series::ATTRIBUTE_ID),
        ];
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
        // Needed to match series relation.
        return true;
    }
}
