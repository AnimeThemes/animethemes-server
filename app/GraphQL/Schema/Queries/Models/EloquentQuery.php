<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models;

use App\GraphQL\Middleware\ResolveBindableArgs;
use App\GraphQL\Schema\Queries\BaseQuery;
use App\GraphQL\Schema\Types\EloquentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

abstract class EloquentQuery extends BaseQuery
{
    public function __construct(
        protected bool $nullable = true,
        protected bool $isList = false,
    ) {
        $this->middleware = array_merge(
            $this->middleware,
            [
                ResolveBindableArgs::class,
            ],
        );

        parent::__construct($nullable, $isList);
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
     * @throws RuntimeException
     */
    public function model(): string
    {
        $baseType = $this->baseType();

        if ($baseType instanceof EloquentType) {
            return $baseType->model();
        }

        throw new RuntimeException(sprintf('The base return rebing type must be an instance of EloquentType, %s given.', $baseType::class));
    }

    protected function query(Builder $builder, array $args): Builder
    {
        return $builder;
    }
}
