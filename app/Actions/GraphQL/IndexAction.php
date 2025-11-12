<?php

declare(strict_types=1);

namespace App\Actions\GraphQL;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\Concerns\Actions\GraphQL\PaginatesModels;
use App\Concerns\Actions\GraphQL\SortsModels;
use App\Enums\GraphQL\SortType;
use App\Exceptions\GraphQL\ClientValidationException;
use App\GraphQL\Schema\Enums\SortableColumns;
use App\GraphQL\Schema\Fields\StringField;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Support\Argument\SortArgument;
use App\GraphQL\Support\Sort\Sort;
use App\Search\Criteria;
use App\Search\Search;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use InvalidArgumentException;

class IndexAction
{
    use ConstrainsEagerLoads;
    use PaginatesModels;
    use SortsModels;

    public function index(Builder $builder, array $args, BaseType $type, ResolveInfo $resolveInfo): Paginator
    {
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
                $this->filter($builder, $args, $type);

                $this->sort($builder, $args, $type);

                $this->constrainEagerLoads($builder, $resolveInfo, $type);
            });

        $first = min(100, Arr::get($args, 'first'));
        $page = Arr::get($args, 'page', 1);

        $maxCount = Config::get('graphql.pagination_values.max_count');
        throw_if($maxCount !== null && $first > $maxCount, ClientValidationException::class, "Maximum first value is {$maxCount}. Got {$first}. Fetch in smaller chuncks.");

        $searchBuilder->withPagination($first, $page);

        $sorts = Arr::get($args, SortArgument::ARGUMENT, []);
        $resolvers = Arr::get(new SortableColumns($type)->getAttributes(), 'resolvers');

        $sortsRaw = [];
        foreach ($sorts as $sort) {
            $direction = Sort::resolveFromEnumCase($sort);
            $resolver = Arr::get($resolvers, Str::remove('_DESC', $sort));
            $column = Arr::get($resolver, SortableColumns::RESOLVER_COLUMN);
            $sortType = Arr::get($resolver, SortableColumns::RESOLVER_SORT_TYPE);
            $isString = Arr::get($resolver, SortableColumns::RESOLVER_FIELD) instanceof StringField;

            if ($sortType === SortType::ROOT) {
                $sortsRaw[$column] = [
                    'direction' => $direction,
                    'isString' => $isString,
                ];
            }

            if ($sortType === SortType::RELATION) {
                $relation = Arr::get($resolver, SortableColumns::RESOLVER_RELATION);
                throw_if($relation === null, InvalidArgumentException::class, "The 'relation' argument is required for the {$column} column with aggregate sort type.");

                $sortsRaw[$column] = [
                    'direction' => $direction,
                    'isString' => $isString,
                    'relation' => $relation,
                ];
            }
        }

        $searchBuilder->withSort($sortsRaw);

        return $searchBuilder->execute();
    }
}
