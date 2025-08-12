<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular;

use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Middleware\ResolveBindableArgs;
use App\GraphQL\Support\Argument\Argument;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;
use RuntimeException;

abstract class EloquentSingularQuery extends BaseQuery
{
    protected $middleware = [
        ResolveBindableArgs::class,
    ];

    public function __construct(
        protected string $name,
    ) {
        parent::__construct($name, nullable: true, isList: false);
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
     * Get the model related to the query.
     *
     * @return class-string<Model>
     *
     * @throws Exception
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

        return Type::nonNull(GraphQL::type($this->baseRebingType()->getName()));
    }

    /**
     * Determine if the return model is trashable.
     */
    protected function isTrashable(): bool
    {
        $baseType = $this->baseRebingType();

        if ($baseType instanceof EloquentType) {
            return in_array(new DeletedAtField(), $baseType->fieldClasses());
        }

        return false;
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        return $args['model'];
    }
}
