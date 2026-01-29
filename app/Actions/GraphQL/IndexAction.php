<?php

declare(strict_types=1);

namespace App\Actions\GraphQL;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\Concerns\Actions\GraphQL\FieldSelection;
use App\Concerns\Actions\GraphQL\PaginatesModels;
use App\Concerns\Actions\GraphQL\SortsModels;
use App\GraphQL\Argument\SortArgument;
use App\GraphQL\Criteria\Sort\RelationSortCriteria;
use App\GraphQL\Criteria\Sort\SortCriteria;
use App\GraphQL\Schema\Enums\SortableColumns;
use App\GraphQL\Schema\Fields\StringField;
use App\GraphQL\Schema\Types\BaseType;
use App\Rules\GraphQL\Argument\FirstArgumentRule;
use App\Search\Criteria;
use App\Search\Search;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class IndexAction
{
    use ConstrainsEagerLoads;
    use FieldSelection;
    use PaginatesModels;
    use SortsModels;

    public function index(Builder $builder, array $args, BaseType $type, ResolveInfo $resolveInfo): Paginator
    {
        $this->withAggregates($builder, $args, $this->getSelection($resolveInfo), $type);

        $this->filter($builder, $args, $type);

        $this->sort($builder, $args, $type);

        $this->constrainEagerLoads($builder, $resolveInfo, $type);

        return $this->paginate($builder, $args);
    }

    public function search(Builder $builder, array $args, BaseType $type, ResolveInfo $resolveInfo): Paginator
    {
        $criteria = new Criteria(Arr::get($args, 'search'));

        $searchBuilder = Search::search($builder->getModel(), $criteria)
            ->passToEloquentBuilder(function (Builder $builder) use ($args, $type, $resolveInfo): void {
                $this->withAggregates($builder, $args, $this->getSelection($resolveInfo), $type);

                $this->filter($builder, $args, $type);

                $this->sort($builder, $args, $type);

                $this->constrainEagerLoads($builder, $resolveInfo, $type);
            });

        // Note: First for searching must not be too high.
        $first = min(100, Arr::get($args, 'first'));
        $page = Arr::get($args, 'page', 1);

        Validator::make(['first' => $first], [
            'first' => ['required', 'integer', 'min:1', new FirstArgumentRule()],
        ])->validate();

        $searchBuilder->withPagination($first, $page);

        $sorts = Arr::get($args, SortArgument::ARGUMENT, []);
        $criteria = Arr::get(new SortableColumns($type)->getAttributes(), 'criteria');

        $sortsRaw = [];
        foreach ($sorts as $sort) {
            /** @var SortCriteria $criterion */
            $criterion = Arr::get($criteria, $sort);

            $column = $criterion->getField()->getColumn();
            $direction = $criterion->getDirection();
            $isString = $criterion->getField() instanceof StringField;

            if ($criterion instanceof RelationSortCriteria) {
                $sortsRaw[$column] = [
                    'direction' => $direction->value,
                    'isString' => $isString,
                    'relation' => $criterion->relation,
                ];
            } else {
                $sortsRaw[$column] = [
                    'direction' => $direction,
                    'isString' => $isString,
                ];
            }
        }

        $searchBuilder->withSort($sortsRaw);

        return $searchBuilder->execute();
    }
}
