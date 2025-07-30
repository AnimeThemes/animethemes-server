<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Reports;

use App\GraphQL\Definition\Mutations\BaseMutation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;

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
                ->required()
                ->directives(['spread' => []]),
        ];

        return $arguments;
    }

    /**
     * The input type of the 'input' argument on the top mutation.
     */
    abstract public function rootInput(): string;

    /**
     * The base return type of the mutation.
     */
    abstract public function baseType(): BaseType;
}
