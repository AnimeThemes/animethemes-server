<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Argument;

use App\Contracts\GraphQL\Fields\SortableField;
use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Directives\SortCustomDirective;
use App\GraphQL\Support\Sort\RandomSort;
use App\GraphQL\Support\SortableColumns;
use Illuminate\Support\Str;

class SortArgument extends Argument
{
    public function __construct(protected BaseType&HasFields $type)
    {
        $suffix = SortableColumns::SUFFIX;

        parent::__construct('sort', "[{$type->getName()}{$suffix}!]");

        $this->directives([
            'sortCustom' => [
                'columns' => json_encode($this->resolveColumns()),
            ],
        ]);
    }

    /**
     * Resolve the columns parameter for the sortCustom directive.
     *
     * @return array<int, array<string, string|int|null>>
     */
    private function resolveColumns(): array
    {
        return collect($this->type->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (Field&SortableField $field) => [
                SortCustomDirective::INPUT_COLUMN => $field->getColumn(),
                SortCustomDirective::INPUT_VALUE => Str::of($field->getName())->snake()->upper()->__toString(),
                SortCustomDirective::INPUT_SORT_TYPE => $field->sortType()->value,
                SortCustomDirective::INPUT_RELATION => method_exists($field, 'relation') ? $field->{'relation'}() : null,
            ])
            // @phpstan-ignore-next-line
            ->push([
                SortCustomDirective::INPUT_VALUE => RandomSort::CASE,
            ])
            ->toArray();
    }
}
