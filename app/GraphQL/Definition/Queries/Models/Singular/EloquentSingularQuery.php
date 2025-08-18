<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Concerns\Actions\GraphQL\SortsModels;
use App\GraphQL\Definition\Queries\Models\EloquentQuery;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Middleware\ResolveBindableArgs;
use App\GraphQL\Support\Argument\Argument;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Rebing\GraphQL\Support\Facades\GraphQL;
use RuntimeException;

abstract class EloquentSingularQuery extends EloquentQuery
{
    use FiltersModels;
    use ConstrainsEagerLoads;

    public function __construct(
        protected string $name,
    ) {
        $this->middleware = array_merge(
            $this->middleware,
            [
                ResolveBindableArgs::class,
            ],
        );

        parent::__construct($name, nullable: true, isList: false);
    }

    /**
     * Authorize the query.
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return Gate::allows('view', [$this->model(), $args]);
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [];
        $baseType = $this->baseRebingType();

        if ($baseType instanceof BaseType) {
            $arguments[] = $this->resolveBindArguments($baseType->fieldClasses());
        }

        return Arr::flatten($arguments);
    }

    /**
     * The return type of the query.
     */
    public function type(): Type
    {
        $rebingType = $this->baseRebingType();

        if (! $rebingType instanceof BaseType) {
            throw new RuntimeException("baseRebingType not defined for query {$this->getName()}");
        }

        return Type::nonNull(GraphQL::type($this->baseRebingType()->getName()));
    }

    /**
     * Resolve the singular record with the binded argument.
     *
     * @return Model
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        /** @var Model $model */
        $model = Arr::get($args, 'model');

        $builder = $this->query($this->model()::query(), $args);

        $this->filter($builder, $args, $this->baseRebingType());

        $builder->whereKey($model->getKey());

        $this->constrainEagerLoads($builder, $resolveInfo, $this->baseRebingType());

        return $builder
            ->firstOrFail();
    }
}
