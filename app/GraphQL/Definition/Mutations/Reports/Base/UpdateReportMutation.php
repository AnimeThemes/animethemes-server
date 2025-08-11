<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Reports\Base;

use App\GraphQL\Definition\Mutations\Reports\BaseReportMutation;
use Illuminate\Support\Str;

abstract class UpdateReportMutation extends BaseReportMutation
{
    /**
     * The input type of the 'input' argument on the top mutation.
     */
    public function rootInput(): string
    {
        return Str::of('Update')
            ->append($this->baseRebingType()->getName())
            ->append('Input')
            ->__toString();
    }
}
