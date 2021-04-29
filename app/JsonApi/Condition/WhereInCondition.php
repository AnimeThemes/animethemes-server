<?php

namespace App\JsonApi\Condition;

use App\Enums\Filter\BinaryLogicalOperator;
use App\Enums\Filter\UnaryLogicalOperator;
use App\JsonApi\Filter\Filter;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\TermsQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class WhereInCondition extends Condition
{
    /**
     * The flag to use the not operator in the condition.
     *
     * @var bool
     */
    public $not;

    /**
     * Create a new condition instance.
     *
     * @param Predicate $predicate
     * @param BinaryLogicalOperator $operator
     * @param bool $not
     * @param string $scope
     */
    final public function __construct(
        Predicate $predicate,
        BinaryLogicalOperator $operator,
        bool $not = false,
        string $scope = ''
    ) {
        parent::__construct($predicate, $operator, $scope);

        $this->not = $not;
    }

    /**
     * Get not operator.
     *
     * @return bool
     */
    public function useNot()
    {
        return $this->not;
    }

    /**
     * Create a new condition instance from query string.
     *
     * @param string $filterParam
     * @param string $filterValues
     * @return Condition
     */
    public static function make(string $filterParam, string $filterValues)
    {
        $scope = '';
        $field = '';
        $operator = BinaryLogicalOperator::AND();
        $not = false;

        $filterParts = Str::of($filterParam)->explode('.');
        while ($filterParts->isNotEmpty()) {
            $filter_part = $filterParts->pop();

            // Set Not
            if (empty($scope) && empty($field) && UnaryLogicalOperator::hasKey(Str::upper($filter_part))) {
                $not = true;
                continue;
            }

            // Set operator
            if (empty($scope) && empty($field) && BinaryLogicalOperator::hasKey(Str::upper($filter_part))) {
                $operator = BinaryLogicalOperator::fromKey(Str::upper($filter_part));
                continue;
            }

            // Set field
            if (empty($scope) && empty($field)) {
                $field = Str::lower($filter_part);
                continue;
            }

            // Set scope
            if (empty($scope) && ! empty($field)) {
                $scope = Str::lower($filter_part);
                continue;
            }
        }

        $expression = new Expression(Str::of($filterValues)->explode(','));

        $predicate = new Predicate($field, null, $expression);

        return new static($predicate, $operator, $not, $scope);
    }

    /**
     * Apply condition to builder through filter.
     *
     * @param Builder $builder
     * @param Filter $filter
     * @return Builder $builder
     */
    public function apply(Builder $builder, Filter $filter)
    {
        return $builder->whereIn(
            $filter->getScope().'.'.$this->getField(),
            $filter->getFilterValues($this),
            $this->getLogicalOperator()->value,
            $this->not
        );
    }

    /**
     * Apply condition to builder through filter.
     *
     * @param BoolQueryBuilder $builder
     * @param Filter $filter
     * @return BoolQueryBuilder $builder
     */
    public function applyElasticsearchFilter(BoolQueryBuilder $builder, Filter $filter)
    {
        $clause = (new TermsQueryBuilder())->terms($filter->getKey(), $filter->getFilterValues($this));

        if (BinaryLogicalOperator::OR()->is($this->getLogicalOperator())) {
            if ($this->not) {
                return $builder->should((new BoolQueryBuilder())->mustNot($clause));
            }

            return $builder->should($clause);
        }

        if ($this->not) {
            return $builder->mustNot($clause);
        }

        return $builder->must($clause);
    }
}
