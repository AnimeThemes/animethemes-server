<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\Enums\GraphQL\TrashedFilter as TrashedFilterEnum;
use App\GraphQL\Argument\Argument;
use App\GraphQL\Criteria\Filter\TrashedFilterCriteria;
use Rebing\GraphQL\Support\Facades\GraphQL;

class TrashedFilter extends Filter
{
    public function argument(): Argument
    {
        return new Argument('trashed', GraphQL::type(class_basename(TrashedFilterEnum::class)))
            ->withDefaultValue(TrashedFilterEnum::WITHOUT);
    }

    public function criteria(mixed $value): TrashedFilterCriteria
    {
        return new TrashedFilterCriteria($value);
    }
}
