<?php declare(strict_types=1);

namespace App\JsonApi\Condition;

use App\Enums\Filter\BinaryLogicalOperator;
use App\Enums\Filter\UnaryLogicalOperator;
use App\JsonApi\Filter\Filter;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\TermsQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class WhereInCondition
 * @package App\JsonApi\Condition
 */
class WhereInCondition extends Condition
{
    /**
     * The flag to use the not operator in the condition.
     *
     * @var bool
     */
    public bool $not;

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
    public function useNot(): bool
    {
        return $this->not;
    }

    /**
     * Create a new condition instance from query string.
     *
     * @param string $filterParam
     * @param mixed $filterValues
     * @return Condition
     */
    public static function make(string $filterParam, mixed $filterValues): Condition
    {
        $scope = '';
        $field = '';
        $operator = BinaryLogicalOperator::AND();
        $not = false;

        $filterParts = Str::of($filterParam)->explode('.');
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set Not
            if (empty($scope) && empty($field) && UnaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                $not = true;
                continue;
            }

            // Set operator
            if (empty($scope) && empty($field) && BinaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $operator = BinaryLogicalOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::error($e->getMessage());
                }
                continue;
            }

            // Set field
            if (empty($scope) && empty($field)) {
                $field = Str::lower($filterPart);
                continue;
            }

            // Set scope
            if (empty($scope) && ! empty($field)) {
                $scope = Str::lower($filterPart);
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
    public function apply(Builder $builder, Filter $filter): Builder
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
    public function applyElasticsearchFilter(BoolQueryBuilder $builder, Filter $filter): BoolQueryBuilder
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
