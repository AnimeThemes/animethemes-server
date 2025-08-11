<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields;

use App\GraphQL\Support\Filter\EqFilter;
use App\GraphQL\Support\Filter\Filter;
use App\GraphQL\Support\Filter\GreaterFilter;
use App\GraphQL\Support\Filter\LesserFilter;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;

abstract class DateTimeTzField extends StringField
{
    /**
     * The filters of the field.
     *
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return [
            new EqFilter($this),
            new LesserFilter($this),
            new GreaterFilter($this),
        ];
    }

    /**
     * Resolve the field.
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        return Arr::get($root, $this->getName());
    }
}
