<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries;

use App\Concerns\GraphQL\ResolvesArguments;
use App\GraphQL\Definition\Queries\Models\Paginator\EloquentPaginatorQuery;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

abstract class BaseQuery extends Query
{
    use ResolvesArguments;

    public function __construct(
        protected string $name,
        protected bool $nullable = true,
        protected bool $isList = false,
    ) {}

    /**
     * Get the attributes of the type.
     *
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->description(),
        ];
    }

    /**
     * Get the name of the query.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * The description of the type.
     */
    abstract public function description(): string;

    /**
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [];

        $baseType = $this->baseRebingType();

        if ($this instanceof EloquentPaginatorQuery) {
            $arguments[] = new Argument('first', Type::nonNull(Type::int()))->withDefaultValue(15);
            $arguments[] = new Argument('page', Type::int());
        }

        // if ($this->resolveSearchAttribute()) {
        //     $arguments[] = new Argument('search', Type::string())->directives(['search' => []]);
        // }

        if ($baseType instanceof BaseType) {
            $arguments[] = $this->resolveFilterArguments($baseType->fieldClasses());
        }

        if ($baseType instanceof BaseType && $this instanceof EloquentPaginatorQuery) {
            $arguments[] = $this->resolveSortArguments($baseType);
        }

        // if ($baseType instanceof BaseType && $this->resolvePaginateAttribute()) {
        //     $arguments[] = $this->resolveSortArguments($baseType);
        // }

        // if ($baseType instanceof BaseType) {
        //     $arguments[] = new WhereArgument($baseType);
        // }

        return Arr::flatten($arguments);
    }

    public function args(): array
    {
        return collect($this->arguments())
            ->mapWithKeys(function (Argument $argument) {
                $defaultValue = $argument->getDefaultValue();

                return [
                    $argument->name => [
                        'name' => $argument->name,
                        'type' => $argument->getType(),

                        ...(! is_null($defaultValue) ? ['defaultValue' => $defaultValue] : []),
                    ],
                ];
            })
            ->toArray();
    }

    public function baseType(): Type
    {
        return GraphQL::type($this->baseRebingType()->getName());
    }

    /**
     * The base return type of the query.
     */
    public function baseRebingType(): ?BaseType
    {
        return null;
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        if (! $this->nullable) {
            if ($this->isList) {
                return Type::nonNull(Type::listOf(Type::nonNull($this->baseType())));
            }

            return Type::nonNull($this->baseType());
        }

        if ($this->isList) {
            return Type::listOf(Type::nonNull($this->baseType()));
        }

        return $this->baseType();
    }
}
