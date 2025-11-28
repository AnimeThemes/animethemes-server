<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Submissions;

use App\GraphQL\Schema\Mutations\BaseMutation;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;

abstract class BaseSubmissionMutation extends BaseMutation
{
    /**
     * The arguments of the mutation.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [
            new Argument('input', $this->rootInput())
                ->required(),
        ];
    }

    public function type(): Type
    {
        return Type::nonNull($this->baseType());
    }

    /**
     * The input type of the 'input' argument on the top mutation.
     */
    abstract public function rootInput(): string;
}
