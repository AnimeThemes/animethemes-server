<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Scope\Scope;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class WhereCriteria.
 */
class WhereCriteria extends Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  Predicate  $predicate
     * @param  BinaryLogicalOperator  $operator
     * @param  Scope  $scope
     */
    final public function __construct(
        Predicate $predicate,
        BinaryLogicalOperator $operator,
        Scope $scope
    ) {
        parent::__construct($predicate, $operator, $scope);
    }

    /**
     * Create a new criteria instance from query string.
     *
     * @param  Scope  $scope
     * @param  string  $filterParam
     * @param  mixed  $filterValues
     * @return static
     */
    public static function make(Scope $scope, string $filterParam, mixed $filterValues): static
    {
        $field = '';
        $comparisonOperator = ComparisonOperator::EQ();
        $logicalOperator = BinaryLogicalOperator::AND();

        $filterParts = Str::of($filterParam)->explode(Criteria::PARAM_SEPARATOR);
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set logical operator
            if (empty($field) && BinaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $logicalOperator = BinaryLogicalOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::error($e->getMessage());
                }
                continue;
            }

            // Set comparison operator
            if (empty($field) && ComparisonOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $comparisonOperator = ComparisonOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::error($e->getMessage());
                }
                continue;
            }

            // Set field
            if (empty($field)) {
                $field = Str::lower($filterPart);
            }
        }

        $expression = new Expression($filterValues);

        return new static(
            new Predicate($field, $comparisonOperator, $expression),
            $logicalOperator,
            $scope
        );
    }

    /**
     * Apply criteria to builder.
     *
     * @param  Builder  $builder
     * @param  Filter  $filter
     * @param  ReadQuery  $query
     * @return Builder
     */
    public function filter(Builder $builder, Filter $filter, ReadQuery $query): Builder
    {
        return $builder->where(
            $builder->qualifyColumn($filter->getColumn()),
            $this->getComparisonOperator()?->value,
            $filter->getFilterValues($this->getFilterValues()),
            $this->getLogicalOperator()->value
        );
    }
}
