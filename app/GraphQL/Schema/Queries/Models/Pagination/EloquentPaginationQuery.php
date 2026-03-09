<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination;

use App\Actions\GraphQL\IndexAction;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Argument\FirstArgument;
use App\GraphQL\Argument\PageArgument;
use App\GraphQL\Schema\Queries\Models\EloquentQuery;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Rebing\GraphQL\Support\Facades\GraphQL;

abstract class EloquentPaginationQuery extends EloquentQuery
{
    public function __construct()
    {
        parent::__construct(false, true);
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $args = collect($args)
            ->filter(fn ($value): bool => $value instanceof Model)
            ->values()
            ->all();

        return ($this->response = Gate::inspect('viewAny', [$this->model(), ...$args]))->allowed();
    }

    /**
     * The arguments of the class resolve as customs class helper.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [
            ...parent::arguments(),

            new FirstArgument(),
            new PageArgument(),
        ];
    }

    /**
     * Resolve the pagination query.
     *
     * @return Paginator
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, IndexAction $action)
    {
        $builder = $this->model()::query();

        $this->query($builder, $args);

        return Arr::has($args, 'search')
            ? $action->search($builder, $args, $this->baseType(), $resolveInfo)
            : $action->index($builder, $args, $this->baseType(), $resolveInfo);
    }

    /**
     * The return type of the query.
     */
    public function type(): Type
    {
        $baseType = $this->baseType();

        return Type::nonNull(GraphQL::paginate($this->baseType()->getName()));
    }
}
