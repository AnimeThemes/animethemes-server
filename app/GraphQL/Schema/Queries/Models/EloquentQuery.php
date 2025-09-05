<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models;

use App\GraphQL\Middleware\ResolveBindableArgs;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Queries\BaseQuery;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\TrashedArgument;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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
        $baseType = $this->baseType();

        if ($baseType instanceof EloquentType) {
            return $baseType->model();
        }

        throw new RuntimeException('The base return rebing type must be an instance of EloquentType, '.get_class($baseType).' given.');
    }

    /**
     * The arguments of the class resolve as customs class helper.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = parent::arguments();

        if (in_array(new DeletedAtField(), $this->baseType()->fieldClasses())) {
            $arguments[] = new TrashedArgument();
        }

        return Arr::flatten($arguments);
    }

    protected function query(Builder $builder, array $args): Builder
    {
        return $builder;
    }
}
