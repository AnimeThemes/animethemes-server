<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Filter;

use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Enums\GraphQL\ComparisonOperator;
use App\Enums\GraphQL\LogicalOperator;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Schema\Enums\FilterableColumns;
use App\GraphQL\Schema\Fields\Base\CountField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\EloquentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WhereConditionsFilterCriteria extends FilterCriteria
{
    use FiltersModels {
        filter as filterModels;
    }

    /**
     * @var Collection<string, Field&FilterableField>
     */
    protected Collection $filterableFields;

    public function __construct(
        protected Filter $filter,
        protected $value,
        protected EloquentType $type,
    ) {
        parent::__construct($filter, $value);
    }

    /**
     * Apply the filtering to the current Eloquent builder.
     */
    public function filter(Builder $builder): Builder
    {
        $this->filterableFields = new FilterableColumns($this->type)->getValues();

        foreach ($this->value as $where) {
            $this->filterWhereCondition($builder, $where, LogicalOperator::AND);
        }

        return $builder;
    }

    /**
     * Apply recursively every where condition.
     *
     * @param  array<string, mixed>  $where
     */
    private function filterWhereCondition(Builder $builder, array $where, LogicalOperator $logical): Builder
    {
        $fieldName = Arr::get($where, 'field');
        $value = Arr::get($where, 'value');

        if (filled($fieldName) && filled($value)) {
            $field = $this->filterableFields->get($fieldName);

            if ($field instanceof CountField) {
                $builder->withCount(Str::replace('Count', '', $field->getName()));
                $builder->having(
                    Str::snake($field->getColumn()),
                    ComparisonOperator::unstrictCoerce(Arr::get($where, 'operator'))->value,
                    Arr::first($field->getFilter()->getFilterValues(Arr::wrap($value))),
                    $logical->value
                );
            } else {
                $builder->where(
                    $field->getColumn(),
                    ComparisonOperator::unstrictCoerce(Arr::get($where, 'operator'))->value,
                    Arr::first($field->getFilter()->getFilterValues(Arr::wrap($value))),
                    $logical->value
                );
            }
        }

        $builder->where(function (Builder $builder) use ($where): void {
            foreach (Arr::array($where, 'AND', []) as $whereCondition) {
                $this->filterWhereCondition($builder, $whereCondition, LogicalOperator::AND);
            }
        });

        $builder->where(function (Builder $builder) use ($where): void {
            foreach (Arr::array($where, 'OR', []) as $whereCondition) {
                $this->filterWhereCondition($builder, $whereCondition, LogicalOperator::OR);
            }
        });

        return $builder;
    }
}
