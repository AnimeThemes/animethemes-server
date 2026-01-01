<?php

declare(strict_types=1);

namespace App\GraphQL\Criteria\Filter;

use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Contracts\GraphQL\Fields\FilterableField;
use App\Enums\GraphQL\ComparisonOperator;
use App\Enums\GraphQL\LogicalOperator;
use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Schema\Enums\FilterableColumns;
use App\GraphQL\Schema\Fields\EnumField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\EloquentType;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use UnitEnum;

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
        protected mixed $value,
        protected EloquentType $type,
    ) {}

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

            if ($field instanceof EnumField) {
                $value = $this->parseEnum($field, $value);
            }

            $builder->where(
                $field->getColumn(),
                ComparisonOperator::unstrictCoerce(Arr::get($where, 'operator'))->value,
                $value,
                $logical->value
            );
        }

        foreach (Arr::array($where, 'AND', []) as $whereCondition) {
            $this->filterWhereCondition($builder, $whereCondition, LogicalOperator::AND);
        }

        foreach (Arr::array($where, 'OR', []) as $whereCondition) {
            $this->filterWhereCondition($builder, $whereCondition, LogicalOperator::OR);
        }

        return $builder;
    }

    /**
     * Enum values are parsed as string and resolved to its backed enum.
     */
    private function parseEnum(EnumField $field, mixed $value): int|string
    {
        $enum = Arr::first(
            $field->enum::cases(),
            fn (UnitEnum $enum): bool => $enum->name === $value
        );

        if (! $enum instanceof BackedEnum) {
            throw new ClientValidationException("'{$value}' does not exist in the ".class_basename($field->enum).' enum.');
        }

        return $enum->value;
    }
}
