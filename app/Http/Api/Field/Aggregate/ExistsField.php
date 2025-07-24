<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Aggregate;

use App\Enums\Http\Api\Field\AggregateFunction;
use App\Enums\Http\Api\Filter\Clause;
use App\Enums\Http\Api\QualifyColumn;
use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Schema\Schema;

abstract class ExistsField extends AggregateField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     * @param  string  $relation
     */
    public function __construct(Schema $schema, string $relation)
    {
        parent::__construct($schema, $relation, AggregateFunction::EXISTS, '*');
    }

    /**
     * Get the filters that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new BooleanFilter(key: $this->alias(), qualifyColumn: QualifyColumn::NO, clause: Clause::HAVING);
    }
}
