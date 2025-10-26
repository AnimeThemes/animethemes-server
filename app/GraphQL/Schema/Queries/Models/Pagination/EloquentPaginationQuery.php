<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination;

use App\Actions\GraphQL\IndexAction;
use App\GraphQL\Schema\Enums\SortableColumns;
use App\GraphQL\Schema\Queries\Models\EloquentQuery;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\EloquentType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Rebing\GraphQL\Support\Facades\GraphQL;
use RuntimeException;

abstract class EloquentPaginationQuery extends EloquentQuery
{
    protected static bool $typesLoaded = false;

    public function __construct(protected string $name)
    {
        parent::__construct($name, false, true);

        if (static::$typesLoaded === false) {
            static::$typesLoaded = true;
            foreach (EloquentType::$typesToLoad as $type) {
                GraphQL::addType(new SortableColumns($type));
            }
        }
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $args = collect($args)
            ->filter(fn ($value): bool => $value instanceof Model)
            ->values()
            ->all();

        return Gate::allows('viewAny', [$this->model(), ...$args]);
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

        return $action->index($builder, $args, $this->baseType(), $resolveInfo);
    }

    /**
     * The return type of the query.
     */
    public function type(): Type
    {
        $baseType = $this->baseType();

        throw_unless($baseType instanceof BaseType, RuntimeException::class, "baseType not defined for query {$this->getName()}");

        return Type::nonNull(GraphQL::paginate($this->baseType()->getName()));
    }
}
