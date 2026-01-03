<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use BackedEnum;
use Closure;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;
use UnitEnum;

class EnumFilter extends Filter
{
    /**
     * @param  class-string<UnitEnum>  $enumClass
     */
    public function __construct(
        string $field,
        protected readonly string $enumClass,
        ?string $column = null,
    ) {
        parent::__construct($field, $column);
    }

    public function getBaseType(): Type
    {
        return GraphQL::type(class_basename($this->enumClass));
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     */
    public function convertFilterValues(array $filterValues): array
    {
        $values = [];

        foreach ($filterValues as $filterValue) {
            if (! $filterValue instanceof BackedEnum) {
                $enum = Arr::first(
                    $this->enumClass::cases(),
                    fn (UnitEnum $enum): bool => $enum->name === $filterValue
                );
            }

            $values[] = $enum ?? $filterValue;
        }

        return $values;
    }

    /**
     * Get the validation rules for the filter.
     */
    protected function getRules(): array
    {
        return [
            'required',
            function ($attribute, mixed $value, Closure $fail): void {
                if (
                    Arr::first($this->enumClass::cases(), fn (UnitEnum $enum): bool => $enum->name === $value) === null
                    && ! $value instanceof BackedEnum
                ) {
                    $fail("'{$value}' does not exist in the ".class_basename($this->enumClass).' enum.');
                }
            },
        ];
    }
}
