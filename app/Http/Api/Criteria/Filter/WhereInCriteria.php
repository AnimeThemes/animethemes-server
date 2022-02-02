<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\UnaryLogicalOperator;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Scope\Scope;
use App\Http\Api\Scope\ScopeParser;
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
        protected bool $not,
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
     * @param  string  $filterParam
     * @param  mixed  $filterValues
     * @return static
     */
    public static function make(string $filterParam, mixed $filterValues): static
    {
        $scope = collect();
        $field = '';
        $operator = BinaryLogicalOperator::AND();
        $not = false;

        $filterParts = Str::of($filterParam)->explode('.');
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set Not
            if ($scope->isEmpty() && empty($field) && UnaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                $not = true;
                continue;
            }

            // Set operator
            if ($scope->isEmpty() && empty($field) && BinaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $operator = BinaryLogicalOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::error($e->getMessage());
                }
                continue;
            }

            // Set field
            if ($scope->isEmpty() && empty($field)) {
                $field = Str::lower($filterPart);
                continue;
            }

            // Set scope
            if (! empty($field)) {
                $scope->prepend(Str::lower($filterPart));
            }
        }

        $expression = new Expression(Str::of($filterValues)->explode(','));

        $predicate = new Predicate($field, null, $expression);

        return new static(
            $predicate,
            $operator,
            $not,
            ScopeParser::parse($scope->join('.'))
        );
    }

    /**
     * Apply criteria to builder.
     *
     * @param  Builder  $builder
     * @param  Filter  $filter
     * @param Query $query
     * @return Builder
     */
    public function filter(Builder $builder, Filter $filter, Query $query): Builder {
        return $builder->whereIn(
            $builder->qualifyColumn($filter->getColumn()),
            $filter->getFilterValues($this->getFilterValues()),
            $this->getLogicalOperator()->value,
            $this->not()
        );
    }
}
