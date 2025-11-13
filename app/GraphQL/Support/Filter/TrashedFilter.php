<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Filter;

use App\GraphQL\Criteria\Filter\TrashedFilterCriteria;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\TrashedArgument;

class TrashedFilter extends Filter
{
    public function __construct()
    {
        // Trashed Filter doesn't need a field so we fake it.
        parent::__construct(new CreatedAtField);
    }

    public function argument(): Argument
    {
        return new TrashedArgument();
    }

    public function criteria(mixed $value): TrashedFilterCriteria
    {
        return new TrashedFilterCriteria($value);
    }
}
