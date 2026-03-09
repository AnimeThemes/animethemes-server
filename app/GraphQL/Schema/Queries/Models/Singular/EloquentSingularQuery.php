<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Singular;

use App\Actions\GraphQL\ShowAction;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Schema\Queries\Models\EloquentQuery;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Rebing\GraphQL\Support\Facades\GraphQL;

abstract class EloquentSingularQuery extends EloquentQuery
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $model = Arr::pull($args, 'model');

        $args = collect($args)
            ->filter(fn ($value): bool => $value instanceof Model)
            ->prepend($model)
            ->values()
            ->all();

        return ($this->response = Gate::inspect('view', $args))->allowed();
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [];

        $arguments[] = $this->resolveBindArguments($this->baseType()->fieldClasses());

        return Arr::flatten($arguments);
    }

    /**
     * The return type of the query.
     */
    public function type(): Type
    {
        return Type::nonNull(GraphQL::type($this->baseType()->getName()));
    }

    /**
     * Resolve the singular record with the binded argument.
     *
     * @return Model
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, ShowAction $action)
    {
        /** @var Model $model */
        $model = Arr::get($args, 'model');

        $builder = $this->model()::query();

        $this->query($builder, $args);

        $builder->whereKey($model->getKey());

        return $action->show($builder, $args, $this->baseType(), $resolveInfo);
    }
}
