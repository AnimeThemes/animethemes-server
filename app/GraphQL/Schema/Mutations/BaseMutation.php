<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations;

use App\Concerns\GraphQL\ResolvesArguments;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Middleware\AuthMutation;
use App\GraphQL\Middleware\ResolveBindableArgs;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Unions\BaseUnion;
use GraphQL\Type\Definition\Type;
use Illuminate\Auth\Access\Response;
use Illuminate\Pipeline\Pipeline;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

abstract class BaseMutation extends Mutation
{
    use ResolvesArguments;

    protected Response $response;

    public function __construct()
    {
        $this->middleware = array_merge(
            $this->middleware,
            [
                AuthMutation::class,
                ResolveBindableArgs::class,
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->name(),
            'description' => $this->description(),
            'baseType' => $this->baseType(),
        ];
    }

    abstract public function name(): string;

    public function description(): string
    {
        return '';
    }

    public function getAuthorizationMessage(): string
    {
        return $this->response->message() ?? 'Unauthorized';
    }

    /**
     * The arguments of the mutation.
     *
     * @return Argument[]
     */
    abstract public function arguments(): array;

    public function type(): Type
    {
        return GraphQL::type($this->baseType()->name());
    }

    /**
     * The base return type of the mutation.
     */
    public function baseType(): BaseType|BaseUnion|null
    {
        return null;
    }

    protected function runHttpMiddlewares(array $middlewares): void
    {
        resolve(Pipeline::class)
            ->send(request())
            ->through($middlewares)
            ->thenReturn();
    }
}
