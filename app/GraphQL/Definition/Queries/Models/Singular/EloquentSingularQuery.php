<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries\Models\Singular;

use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Queries\BaseQuery;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Support\Argument\Argument;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class EloquentSingularQuery extends BaseQuery
{
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
        $baseType = $this->baseType();

        if ($baseType instanceof BaseType) {
            $arguments[] = $this->resolveBindArguments($baseType->fields());
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
        $baseType = $this->baseType();

        if ($baseType instanceof EloquentType) {
            return $baseType->model();
        }

        throw new Exception('The base return type must be an instance of EloquentType, '.get_class($baseType).' given.');
    }

    /**
     * Determine if the return model is trashable.
     */
    protected function isTrashable(): bool
    {
        $baseType = $this->baseType();

        if ($baseType instanceof EloquentType) {
            return in_array(new DeletedAtField(), $baseType->fields());
        }

        return false;
    }
}
