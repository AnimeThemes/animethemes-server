<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\UnaryLogicalOperator;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Scope\Scope;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class WhereInCriteria.
 */
class WhereInCriteria extends Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  Predicate  $predicate
     * @param  BinaryLogicalOperator  $operator
     * @param  bool  $not
     * @param  Scope  $scope
     */
    final public function __construct(
        Predicate $predicate,
        BinaryLogicalOperator $operator,
        protected readonly bool $not,
        Scope $scope
    ) {
        parent::__construct($predicate, $operator, $scope);
    }

    /**
     * Get not operator.
     *
     * @return bool
     */
    public function not(): bool
    {
        return $this->not;
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
        $operator = BinaryLogicalOperator::AND();
        $not = false;

        $filterParts = Str::of($filterParam)->explode(Criteria::PARAM_SEPARATOR);
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set Not
            if (empty($field) && UnaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                $not = true;
                continue;
            }

            // Set operator
            if (empty($field) && BinaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $operator = BinaryLogicalOperator::fromKey(Str::upper($filterPart));
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

        $expression = new Expression(Str::of($filterValues)->explode(Criteria::VALUE_SEPARATOR));

        return new static(
            new Predicate($field, null, $expression),
            $operator,
            $not,
            $scope
        );
    }

    /**
     * Apply criteria to builder.
     *
     * @param  Builder  $builder
     * @param  Filter  $filter
     * @param  Query  $query
     * @return Builder
     */
    public function filter(Builder $builder, Filter $filter, Query $query): Builder
    {
        return $builder->whereIn(
            $builder->qualifyColumn($filter->getColumn()),
            $filter->getFilterValues($this->getFilterValues()),
            $this->getLogicalOperator()->value,
            $this->not()
        );
    }
}
