<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\Enums\GraphQL\Filter\TrashedFilter as TrashedFilterEnum;
use App\GraphQL\Argument\Argument;
use Rebing\GraphQL\Support\Facades\GraphQL;

class TrashedFilter extends EnumFilter
{
    public function __construct()
    {
        parent::__construct('trashed', TrashedFilterEnum::class);
    }

    public function getArguments(): array
    {
        return [
            new Argument('trashed', GraphQL::type(class_basename(TrashedFilterEnum::class)))
                ->withDefaultValue(TrashedFilterEnum::WITHOUT),
        ];
    }
}
