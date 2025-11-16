<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\Concerns\GraphQL\ResolvesArguments;
use App\GraphQL\Schema\Types\BaseType;
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

    public function getColumn(): string
    {
        return $this->column;
    }

    public function description(): string
    {
        return '';
    }

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
     * @return array<string, array<string, mixed>>
     */
    public function args(): array
    {
        return collect($this->arguments())
            ->mapWithKeys(fn (Argument $argument): array => [
                $argument->getName() => [
                    'name' => $argument->getName(),
                    'type' => $argument->getType(),

                    ...(is_null($argument->getDefaultValue()) ? [] : ['defaultValue' => $argument->getDefaultValue()]),
                ],
            ])
            ->toArray();
    }

    abstract public function baseType(): Type|BaseType;

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return Arr::get($root, $this->column);
    }
}
