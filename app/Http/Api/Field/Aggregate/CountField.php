<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Aggregate;

use App\Enums\Http\Api\Field\AggregateFunction;
use App\Enums\Http\Api\Filter\Clause;
use App\Enums\Http\Api\QualifyColumn;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\IntFilter;
use App\Http\Api\Schema\Schema;

abstract class CountField extends AggregateField
{
    public function __construct(Schema $schema, string $relation)
    {
        parent::__construct($schema, $relation, AggregateFunction::COUNT, '*');
    }

    public function getFilter(): Filter
    {
        return new IntFilter(key: $this->alias(), qualifyColumn: QualifyColumn::NO, clause: Clause::HAVING);
    }
}
