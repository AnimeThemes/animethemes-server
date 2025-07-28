<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations;

use App\Concerns\GraphQL\ResolvesArguments;
use App\Concerns\GraphQL\ResolvesAttributes;
use App\Concerns\GraphQL\ResolvesDirectives;
use App\GraphQL\Support\Argument;
use Exception;
use GraphQL\Type\Definition\Type;

/**
 * Clas BaseMutation.
 */
abstract class BaseMutation
{
    use ResolvesArguments;
    use ResolvesAttributes;
    use ResolvesDirectives;

    public function __construct(
        protected string $name,
    ) {}

    /**
     * Mount the mutation and return its string representation.
     */
    public function toGraphQLString(): string
    {
        $directives = $this->resolveDirectives($this->directives());

        $arguments = $this->buildArguments($this->arguments());

        return "
            \"\"\"{$this->description()}\"\"\"
            {$this->name}{$arguments}: {$this->getType()->__toString()} {$directives}
        ";
    }

    /**
     * The arguments of the mutation.
     *
     * @return Argument[]
     */
    abstract public function arguments(): array;

    /**
     * The directives of the mutation.
     *
     * @return array<string, array>
     *
     * @throws Exception
     */
    public function directives(): array
    {
        $field = $this->resolveFieldAttribute();

        if ($field === null) {
            throw new Exception("The mutation {$this->name} must implement an UseFieldDirective attribute.");
        }

        return [
            'field' => [
                'resolver' => $field,
            ],
        ];
    }

    /**
     * The type returned by the mutation.
     */
    abstract public function getType(): Type;

    /**
     * The base return type of the mutation.
     */
    abstract public function baseType(): Type;

    /**
     * The description of the mutation.
     */
    abstract public function description(): string;
}
