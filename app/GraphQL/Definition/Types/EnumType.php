<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use Rebing\GraphQL\Support\EnumType as BaseEnumType;

/**
 * Dynamic enum type to build the {Type}SortableColumns enums.
 */
class EnumType extends BaseEnumType
{
    public function __construct(protected string $enumClass) {}

    /**
     * Get the attributes of the type.
     *
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => class_basename($this->enumClass),
            'values' => $this->getValues(),
        ];
    }

    /**
     * Get the values of the enum.
     *
     * @return array<string, string>
     */
    private function getValues(): array
    {
        return collect($this->enumClass::cases())
            ->mapWithKeys(fn ($case) => [$case->name => $case->name])
            ->toArray();
    }
}
