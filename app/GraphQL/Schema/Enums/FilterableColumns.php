<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Enums;

use App\Contracts\GraphQL\Fields\FilterableField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\EnumType;

/**
 * Dynamic enum type to build the {Type}FilterableColumns enums.
 */
class FilterableColumns extends EnumType
{
    final public const string SUFFIX = 'FilterableColumns';

    public function __construct(
        protected BaseType $type,
        protected ?PivotType $pivotType = null,
        protected ?BelongsToMany $relation = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->getName(),
            'values' => $this->getValues()->keys()->all(),
        ];
    }

    public function getName(): string
    {
        $name = $this->pivotType instanceof PivotType
            ? $this->pivotType->getName()
            : $this->type->getName();

        return $name.self::SUFFIX;
    }

    /**
     * @return Collection<int, Field&FilterableField>
     */
    private function getFilterableFields(): Collection
    {
        return collect($this->type->fieldClasses())
            ->filter(fn (Field $field): bool => $field instanceof FilterableField);
    }

    /**
     * @return Collection<string, Field&FilterableField>
     */
    public function getValues(): Collection
    {
        return $this->getFilterableFields()
            ->mapWithKeys(fn (Field $field): array => [Str::of($field->getName())->snake()->upper()->__toString() => $field]);
    }
}
