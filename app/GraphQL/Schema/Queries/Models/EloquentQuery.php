<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models;

use App\GraphQL\Middleware\ResolveBindableArgs;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Queries\BaseQuery;
use App\GraphQL\Schema\Types\EloquentType;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

abstract class EloquentQuery extends BaseQuery
{
    public function __construct(
        protected string $name,
        protected bool $nullable = true,
        protected bool $isList = false,
    ) {
        $this->middleware = array_merge(
            $this->middleware,
            [
                ResolveBindableArgs::class,
            ],
        );

        parent::__construct($name, $nullable, $isList);
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            ...parent::attributes(),

            'model' => $this->model(),
        ];
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

    /**
     * Manage the query.
     */
    protected function query(Builder $builder, array $args): Builder
    {
        return $builder;
    }
}
