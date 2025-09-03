<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination;

use App\Concerns\Actions\GraphQL\PaginatesModels;
use App\Concerns\Actions\GraphQL\SearchModels;
use App\Concerns\Actions\GraphQL\SortsModels;
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
use Rebing\GraphQL\Support\SelectFields;
use RuntimeException;

abstract class EloquentPaginationQuery extends EloquentQuery
{
    use PaginatesModels;
    use SearchModels;
    use SortsModels;

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
            ->filter(fn ($value) => $value instanceof Model)
            ->values()
            ->all();

        return Gate::allows('viewAny', [$this->model(), ...$args]);
    }

    /**
     * Resolve the pagination query.
     *
     * @return Paginator
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectField)
    {
        $model = $this->model();
        $builder = $model::query();

        $this->query($builder, $args);

        $this->search($builder, $args);

        $this->filter($builder, $args, $this->baseRebingType());

        $this->sort($builder, $args, $this->baseRebingType());

        $this->constrainEagerLoads($builder, $resolveInfo, $this->baseRebingType());

        return $this->paginate($builder, $args);
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

        return Type::nonNull(GraphQL::paginate($this->baseRebingType()->getName()));
    }
}
