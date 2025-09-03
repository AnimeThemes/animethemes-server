<?php

declare(strict_types=1);

namespace App\GraphQL\Support;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

final readonly class InputField
{
    public function __construct(
        protected string $name,
        protected Type|string $type,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): Type
    {
        if (is_string($this->type)) {
            return GraphQL::type($this->type);
        }

        return $this->type;
    }

    public function getRules(): array
    {
        return [];
    }
}
