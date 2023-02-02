<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Base;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Field\AggregateField;
use App\Http\Api\Field\Field;
use App\Http\Api\Filter\HasFilter;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Scope\ScopeParser;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Scout\Elasticsearch\Elasticsearch;
use App\Scout\Search;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use RuntimeException;

/**
 * Class EloquentReadQuery.
 */
abstract class EloquentReadQuery extends ReadQuery
{
    /**
     * Prepare model for json resource.
     *
     * @param  Model  $model
     * @return BaseResource
     */
    public function show(Model $model): BaseResource
    {
        $schema = $this->schema();

        // eager load relations with constraints
        $model->load($this->constrainEagerLoads());

        // Load aggregate relation values
        $fieldCriteria = $this->getFieldCriteria($schema->type());
        collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof AggregateField && $field->shouldAggregate($this))
            ->each(fn (AggregateField $selectedAggregate) => $selectedAggregate->load($model));

        return $this->resource($model);
    }

    /**
     * Prepare models for resource collection.
     *
     * @return BaseCollection
     */
    public function index(): BaseCollection
    {
        $schema = $this->schema();

        // initialize builder, returning early if not resolved
        $builder = $this->indexBuilder();

        // eager load relations with constraints
        $builder->with($this->constrainEagerLoads());

        // select fields
        $fieldCriteria = $this->getFieldCriteria($schema->type());
        $selectedFields = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SelectableField && $field->shouldSelect($this))
            ->map(fn (Field $field) => $field->getColumn());
        $builder->select($builder->qualifyColumns($selectedFields->all()));

        // Load aggregate relation values
        collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof AggregateField && $field->shouldAggregate($this))
            ->each(fn (AggregateField $selectedAggregate) => $selectedAggregate->with($builder));

        // apply filters
        $scope = ScopeParser::parse($schema->type());
        foreach ($this->getFilterCriteria() as $criteria) {
            foreach ($schema->filters() as $filter) {
                if ($criteria->shouldFilter($filter, $scope)) {
                    $criteria->filter($builder, $filter, $this);
                }
            }
        }

        // special case: only apply HasFilter to top-level models
        if (! empty($schema->allowedIncludes())) {
            $hasFilter = new HasFilter($schema->allowedIncludes());
            foreach ($this->getFilterCriteria() as $criteria) {
                if ($criteria->shouldFilter($hasFilter, $scope)) {
                    $criteria->filter($builder, $hasFilter, $this);
                }
            }
        }

        // apply sorts
        foreach ($this->getSortCriteria() as $sortCriterion) {
            foreach ($schema->sorts() as $sort) {
                if ($sortCriterion->shouldSort($sort, $scope)) {
                    $sortCriterion->sort($builder, $sort);
                }
            }
        }

        // paginate
        $paginationCriteria = $this->getPagingCriteria(PaginationStrategy::OFFSET());
        $collection = $paginationCriteria !== null
            ? $paginationCriteria->paginate($builder)
            : $builder->get();

        // return paginated resource collection
        return $this->collection($collection);
    }

    /**
     * Constrain eager loads by binding callbacks that filter on the relations.
     *
     * @return array
     */
    public function constrainEagerLoads(): array
    {
        $constrainedEagerLoads = [];

        $schema = $this->schema();

        $includeCriteria = $this->getIncludeCriteria($schema->type());
        if ($includeCriteria === null) {
            return $constrainedEagerLoads;
        }

        foreach ($includeCriteria->getPaths() as $allowedIncludePath) {
            $relationSchema = $schema->relation($allowedIncludePath);
            if ($relationSchema === null) {
                throw new RuntimeException("Unknown relation '$allowedIncludePath' for type '{$schema->type()}'.");
            }

            $scope = ScopeParser::parse($allowedIncludePath);
            $constrainedEagerLoads[$allowedIncludePath] = function (Relation $relation) use ($scope, $relationSchema) {
                $relationBuilder = $relation->getQuery();

                // select fields
                $fieldCriteria = $this->getFieldCriteria($relationSchema->type());
                $selectedFields = collect($relationSchema->fields())
                    ->filter(fn (Field $field) => $field instanceof SelectableField && $field->shouldSelect($this))
                    ->map(fn (Field $field) => $field->getColumn());
                $relationBuilder->select($relationBuilder->qualifyColumns($selectedFields->all()));

                // Load aggregate relation values
                collect($relationSchema->fields())
                    ->filter(fn (Field $field) => $field instanceof AggregateField && $field->shouldAggregate($this))
                    ->each(fn (AggregateField $selectedAggregate) => $selectedAggregate->with($relationBuilder));

                // apply filters
                foreach ($this->getFilterCriteria() as $criteria) {
                    foreach ($relationSchema->filters() as $filter) {
                        if ($criteria->shouldFilter($filter, $scope)) {
                            $criteria->filter($relationBuilder, $filter, $this);
                        }
                    }
                }

                // apply sorts
                foreach ($this->getSortCriteria() as $sortCriterion) {
                    foreach ($relationSchema->sorts() as $sort) {
                        if ($sortCriterion->shouldSort($sort, $scope)) {
                            $sortCriterion->sort($relationBuilder, $sort);
                        }
                    }
                }
            };
        }

        return $constrainedEagerLoads;
    }

    /**
     * Prepare models for sear.
     *
     * @param  PaginationStrategy  $paginationStrategy
     * @return BaseCollection
     *
     * @throws RuntimeException
     */
    public function search(PaginationStrategy $paginationStrategy): BaseCollection
    {
        $search = static::getSearch();

        if ($search !== null && $search->shouldSearch($this)) {
            return $search->search($this, $paginationStrategy);
        }

        // Let developer know why search can't be performed
        $driver = Config::get('scout.driver');
        $term = $this->getSearchCriteria()?->getTerm();
        throw new RuntimeException("Can't search for term '$term' with driver '$driver'. Please configure supported driver.");
    }

    /**
     * Get the search instance.
     *
     * @return Search|null
     */
    protected static function getSearch(): ?Search
    {
        return match (Config::get('scout.driver')) {
            'elastic' => App::make(Elasticsearch::class),
            default => null,
        };
    }

    /**
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    abstract public function schema(): EloquentSchema;

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    abstract public function indexBuilder(): Builder;

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    abstract public function resource(mixed $resource): BaseResource;

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    abstract public function collection(mixed $resource): BaseCollection;
}
