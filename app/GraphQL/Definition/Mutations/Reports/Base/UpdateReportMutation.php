<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Reports\Base;

use App\GraphQL\Definition\Input\Input;
use App\GraphQL\Definition\Mutations\Reports\BaseReportMutation;
use App\GraphQL\Definition\Types\BaseType;
use Illuminate\Support\Str;

abstract class UpdateReportMutation extends BaseReportMutation
{
    /**
     * The directives of the mutation.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            'update' => [],
        ];
    }

    /**
     * The input type of the 'input' argument on the top mutation.
     */
    public function rootInput(): string
    {
        return Str::of('Update')
            ->append($this->baseType()->getName())
            ->append('Input')
            ->__toString();
    }

    /**
     * The base return type of the mutation.
     */
    abstract public function baseType(): BaseType;

    /**
     * The type returned by the mutation.
     */
    public function getType(): BaseType
    {
        return $this->baseType();
    }
}
