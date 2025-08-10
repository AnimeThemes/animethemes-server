<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator;

use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Concerns\Actions\GraphQL\SortsModels;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Support\SortableColumns;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;

abstract class EloquentPaginatorQuery extends BaseQuery
{
    use FiltersModels;
    use SortsModels;

    public function __construct(protected string $name)
    {
        parent::__construct($name, false, true);

        GraphQL::addType(new SortableColumns($this->baseRebingType()));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $model = $this->model();

        $builder = $model::query();

        $this->filter($builder, $args, $this->baseRebingType());

        $this->sort($builder, $args, $this->baseRebingType());

        $first = Arr::get($args, 'first');
        $page = Arr::get($args, 'page');

        return $builder->paginate($first, page: $page);
    }

    /**
     * @return class-string<Model>
     */
    public function model(): string
    {
        /** @var EloquentType $type */
        $type = $this->baseRebingType();

        return $type->model();
    }

    /**
     * The return type of the query.
     */
    public function type(): Type
    {
        return Type::nonNull(GraphQL::paginate(Arr::get($this->baseRebingType()->getAttributes(), 'name')));
    }

    /**
     * Determine if the return model is trashable.
     */
    protected function isTrashable(): bool
    {
        $baseType = $this->baseType();

        if ($baseType instanceof EloquentType) {
            return in_array(new DeletedAtField(), $baseType->fieldClasses());
        }

        return false;
    }
}
