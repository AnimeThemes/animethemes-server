<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\GraphQL\Filter\EqFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\GreaterFilter;
use App\GraphQL\Filter\LesserFilter;
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
     * @return array<string, array<string, mixed>>
     */
    public function args(): array
    {
        return [
            'format' => [
                'type' => Type::nonNull(Type::string()),
                'defaultValue' => 'Y-m-d H:i:s',
            ],
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
