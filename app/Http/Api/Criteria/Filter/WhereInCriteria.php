<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\UnaryLogicalOperator;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\TermsQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class WhereInCriteria.
 */
class WhereInCriteria extends Criteria
{
    /**
     * The flag to use the not operator in the criteria.
     *
     * @var bool
     */
    public bool $not;

    /**
     * Create a new criteria instance.
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
    public function not(): bool
    {
        return $this->not;
    }

    /**
     * Create a new criteria instance from query string.
     *
     * @param string $filterParam
     * @param mixed $filterValues
     * @return static
     */
    public static function make(string $filterParam, mixed $filterValues): static
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
     * Apply criteria to builder.
     *
     * @param Builder $builder
     * @param string $column
     * @param array $filterValues
     * @return Builder
     */
    public function apply(Builder $builder, string $column, array $filterValues): Builder
    {
        return $builder->whereIn(
            $builder->qualifyColumn($column),
            $filterValues,
            $this->getLogicalOperator()->value,
            $this->not()
        );
    }

    /**
     * Apply criteria to builder.
     *
     * @param BoolQueryBuilder $builder
     * @param string $column
     * @param array $filterValues
     * @return BoolQueryBuilder
     */
    public function applyElasticsearchFilter(
        BoolQueryBuilder $builder,
        string $column,
        array $filterValues
    ): BoolQueryBuilder {
        $clause = (new TermsQueryBuilder())->terms($column, $filterValues);

        if (BinaryLogicalOperator::OR()->is($this->getLogicalOperator())) {
            if ($this->not()) {
                return $builder->should((new BoolQueryBuilder())->mustNot($clause));
            }

            return $builder->should($clause);
        }

        if ($this->not()) {
            return $builder->mustNot($clause);
        }

        return $builder->must($clause);
    }
}
