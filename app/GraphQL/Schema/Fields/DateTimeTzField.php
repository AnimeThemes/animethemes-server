<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields;

use App\GraphQL\Filter\DateTimeTzFilter;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

abstract class DateTimeTzField extends StringField
{
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

    public function getFilter(): DateTimeTzFilter
    {
        return new DateTimeTzFilter($this->getName(), $this->getColumn())
            ->useEq()
            ->useLt()
            ->useGt();
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        $format = Arr::get($args, 'format');

        /** @var Carbon $field */
        $field = $root->{$this->getColumn()};

        return $field->format($format);
    }
}
