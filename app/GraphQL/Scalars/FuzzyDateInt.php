<?php

declare(strict_types=1);

namespace App\GraphQL\Scalars;

use App\ValueObjects\FuzzyDate;
use GraphQL\Error\Error;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;

class FuzzyDateInt extends ScalarType
{
    /**
     * Serializes an internal value to include in a response.
     */
    public function serialize(mixed $value): ?int
    {
        return $this->normalize($value);
    }

    /**
     * Parses an externally provided value from query variables.
     */
    public function parseValue(mixed $value): ?int
    {
        return $this->normalize($value);
    }

    /**
     * Parses an externally provided literal value hardcoded in the GraphQL query.
     *
     * @param  \GraphQL\Language\AST\ValueNode&Node  $valueNode
     * @param  array<string, mixed>|null  $variables
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null): ?int
    {
        if (! $valueNode instanceof IntValueNode && ! $valueNode instanceof StringValueNode) {
            throw new Error("FuzzyDateInt cannot represent non-integer value: {$valueNode->kind}", $valueNode);
        }

        return $this->normalize($valueNode->value);
    }

    private function normalize(mixed $value): int
    {
        return (int) FuzzyDate::fromString(str_pad((string) $value, 8, '0', STR_PAD_RIGHT))->__toString();
    }
}
