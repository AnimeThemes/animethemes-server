<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Paginator;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Concerns\Actions\GraphQL\PaginatesModels;
use App\Concerns\Actions\GraphQL\SortsModels;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\EloquentType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\SelectFields;
use RuntimeException;

abstract class EloquentPaginatorQuery extends BaseQuery
{
    use ConstrainsEagerLoads;
    use FiltersModels;
    use PaginatesModels;
    use SortsModels;

    public function __construct(protected string $name)
    {
        parent::__construct($name, false, true);
    }

    /**
     * Get the attributes of the type.
     *
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            ...parent::attributes(),

            'model' => $this->model(),
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectField)
    {
        $data = Arr::get($resolveInfo->getFieldSelection(1), 'data');

        $model = $this->model();
        $builder = $model::query();

        $this->filter($builder, $args, $this->baseRebingType());

        $this->sort($builder, $args, $this->baseRebingType());

        $this->constrainEagerLoads($builder, $resolveInfo, $this->baseRebingType());

        return $this->paginate($builder, $args);
    }

    /**
     * @return class-string<Model>
     */
    public function model(): string
    {
        $baseType = $this->baseRebingType();

        if ($baseType instanceof EloquentType) {
            return $baseType->model();
        }

        throw new RuntimeException('The base return rebing type must be an instance of EloquentType, '.get_class($baseType).' given.');
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

    /**
     * Determine if the return model is trashable.
     */
    protected function isTrashable(): bool
    {
        $baseType = $this->baseRebingType();

        return in_array(new DeletedAtField(), $baseType->fieldClasses());
    }
}
