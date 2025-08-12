<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\Concerns\GraphQL\ResolvesArguments;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;

abstract class Field
{
    use ResolvesArguments;

    public function __construct(
        protected string $column,
        protected ?string $name = null,
        protected bool $nullable = true,
    ) {}

    /**
     * Get the name of the field.
     * By default, the name will be the column in camelCase.
     */
    public function getName(): string
    {
        return $this->name ?? Str::camel($this->column);
    }

    /**
     * Get the column of the field.
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return '';
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        $baseType = $this->baseType();

        $type = $baseType instanceof BaseType
            ? GraphQL::type($baseType->getName())
            : $baseType;

        if (! $this->nullable) {
            return Type::nonNull($type);
        }

        return $type;
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [];
    }

    /**
     * The args for the field.
     *
     * @return array<string, array<string, mixed>>
     */
    public function args(): array
    {
        return collect($this->arguments())
            ->mapWithKeys(fn (Argument $argument) => [
                $argument->name => [
                    'name' => $argument->name,
                    'type' => $argument->getType(),

                    ...(! is_null($argument->getDefaultValue()) ? ['defaultValue' => $argument->getDefaultValue()] : []),
                ],
            ])
            ->toArray();
    }

    /**
     * The type returned by the field.
     */
    abstract public function baseType(): Type|BaseType;

    /**
     * Resolve the field.
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return Arr::get($root, $this->column);
    }
}
