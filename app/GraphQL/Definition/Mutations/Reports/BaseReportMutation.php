<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Reports;

use App\GraphQL\Definition\Mutations\BaseMutation;
use App\GraphQL\Support\Argument\Argument;
use GraphQL\Type\Definition\Type;

abstract class BaseReportMutation extends BaseMutation
{
    /**
     * The arguments of the mutation.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        $arguments = [
            new Argument('input', $this->rootInput())
                ->required(),
        ];

        return $arguments;
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::nonNull($this->baseRebingType());
    }

    /**
     * The input type of the 'input' argument on the top mutation.
     */
    abstract public function rootInput(): string;
}
