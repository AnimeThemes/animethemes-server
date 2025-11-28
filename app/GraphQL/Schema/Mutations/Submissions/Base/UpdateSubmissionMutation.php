<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Submissions\Base;

use App\GraphQL\Schema\Mutations\Submissions\BaseSubmissionMutation;
use Illuminate\Support\Str;

abstract class UpdateSubmissionMutation extends BaseSubmissionMutation
{
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
}
