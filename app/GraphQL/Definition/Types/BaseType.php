<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use App\Concerns\GraphQL\ResolvesDirectives;
use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\Fields\SortableField;
use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\Enums\GraphQL\SortDirection;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Support\Relations\Relation;
use App\GraphQL\Support\Sort\RandomSort;
use App\GraphQL\Support\Sort\Sort;
use GraphQL\Type\Definition\ObjectType;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

abstract class BaseType extends ObjectType
{
    use ResolvesDirectives;

    public function __construct()
    {
        // This is just a fake as we are defining the types through strings.
        parent::__construct(['name' => $this->getName(), 'fields' => []]);
    }

    /**
     * Mount the type definition string.
     *
     * @throws RuntimeException
     */
    public function toGraphQLString(): string
    {
        $fields = [];
        if ($this instanceof HasFields) {
            $fields[] = collect($this->fields())
                ->filter(fn (Field $field) => $field instanceof DisplayableField && $field->canBeDisplayed())
                ->map(
                    fn (Field $field) => Str::of('"""')
                        ->append($field->description())
                        ->append('"""')
                        ->newLine()
                        ->append($field->__toString())
                        ->__toString()
                )
                ->toArray();
        }

        if ($this instanceof HasRelations) {
            $fields[] = Arr::map($this->relations(), fn (Relation $relation) => $relation->__toString());
        }

        if (blank($fields)) {
            throw new RuntimeException("There are no fields for the type {$this->getName()}.");
        }

        return Str::of('"""')
            ->append($this->getDescription())
            ->append('"""')
            ->newLine()
            ->append('type ')
            ->append($this->getName())
            ->append(' ')
            ->append($this->resolveDirectives($this->directives()))
            ->append(' {')
            ->newLine()
            ->append(implode(PHP_EOL, Arr::flatten($fields)))
            ->newLine()
            ->append('}')
            ->__toString();
    }

    /**
     * The name displayed of type.
     * By default, it will base on the class name without 'Type'.
     */
    public function getName(): string
    {
        return Str::of(class_basename($this))
            ->remove('Type')
            ->__toString();
    }

    /**
     * The description of the type.
     */
    abstract public function getDescription(): string;

    /**
     * Get the sorts of the resource.
     *
     * @return Collection<int, Sort>
     */
    public function sorts(): Collection
    {
        if (! $this instanceof HasFields) {
            return collect();
        }

        return collect($this->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (Field $field) => [new Sort($field->getName(), SortDirection::ASC), new Sort($field->getName(), SortDirection::DESC)])
            ->flatten()
            ->push(new RandomSort());
    }

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [];
    }
}
