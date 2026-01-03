<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\Exceptions\GraphQL\ClientValidationException;
use BackedEnum;
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

                if (! $enum instanceof BackedEnum) {
                    throw new ClientValidationException("'{$filterValue}' does not exist in the ".class_basename($this->enumClass).' enum.');
                }
            }

            $values[] = $enum ?? $filterValue;
        }

        return $values;
    }
}
