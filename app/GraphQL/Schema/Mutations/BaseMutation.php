<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations;

use App\Concerns\GraphQL\ResolvesArguments;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Middleware\ResolveBindableArgs;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Unions\BaseUnion;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Auth\Access\Response;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

abstract class BaseMutation extends Mutation
{
    use ResolvesArguments;

    protected Response $response;

    public function __construct(
        protected string $name,
    ) {
        $this->middleware = array_merge(
            $this->middleware,
            [
                ResolveBindableArgs::class,
            ],
        );
    }

    public function getAuthorizationMessage(): string
    {
        return $this->response->message() ?? 'Unauthorized';
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->description(),
            'baseType' => $this->baseType(),
        ];
    }

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
    public function toType(): Type
    {
        return GraphQL::type($this->baseType()->getName());
    }

    /**
     * The base return type of the mutation.
     */
    public function baseType(): BaseType|BaseUnion|null
    {
        return null;
    }

    abstract public function description(): string;

    /**
     * @param  array<string, mixed>  $args
     */
    abstract public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed;
}
