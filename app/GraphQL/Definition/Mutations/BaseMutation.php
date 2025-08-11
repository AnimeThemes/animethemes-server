<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations;

use App\Concerns\GraphQL\ResolvesArguments;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Unions\BaseUnion;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

/**
 * Clas BaseMutation.
 */
abstract class BaseMutation extends Mutation
{
    use ResolvesArguments;

    public function __construct(
        protected string $name,
    ) {}

    /**
     * Get the attributes of the mutation.
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
     * The arguments of the mutation.
     *
     * @return Argument[]
     */
    abstract public function arguments(): array;

    /**
     * Convert the rebing type to a GraphQL type.
     */
    public function baseType(): Type
    {
        return GraphQL::type($this->baseRebingType()->getName());
    }

    /**
     * The base return type of the mutation.
     */
    public function baseRebingType(): BaseType|BaseUnion|null
    {
        return null;
    }

    /**
     * The description of the mutation.
     */
    abstract public function description(): string;

    /**
     * Resolve the mutation.
     *
     * @param  array<string, mixed>  $args
     */
    abstract public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed;
}
