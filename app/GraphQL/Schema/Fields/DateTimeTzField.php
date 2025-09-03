<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Filter\EqFilter;
use App\GraphQL\Support\Filter\Filter;
use App\GraphQL\Support\Filter\GreaterFilter;
use App\GraphQL\Support\Filter\LesserFilter;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

abstract class DateTimeTzField extends StringField
{
    /**
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
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [
            new Argument('format', Type::string())
                ->required()
                ->withDefaultValue('Y-m-d H:i:s'),
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        $format = Arr::get($args, 'format');

        /** @var Carbon $field */
        $field = $root->{$this->getColumn()};

        return $field->format($format);
    }
}
