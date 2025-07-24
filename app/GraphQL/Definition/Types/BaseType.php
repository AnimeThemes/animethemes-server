<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use App\Concerns\GraphQL\ResolvesDirectives;
use App\Contracts\GraphQL\Fields\DisplayableField;
use App\Contracts\GraphQL\HasDirectives;
use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Relations\Relation;
use GraphQL\Type\Definition\ObjectType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;

abstract class BaseType extends ObjectType
{
    use ResolvesDirectives;

    public function __construct()
    {
        $fields = [];
        if ($this instanceof HasFields) {
            $fields[] = collect($this->fields())
                ->filter(fn (Field $field) => $field instanceof DisplayableField && $field->canBeDisplayed())
                ->mapWithKeys(fn (Field $field) => [
                    $field->getName() => [
                        'description' => $field->description(),
                        'type' => $field->getType(),
                        'resolve' => fn ($root) => $field->resolve($root),
                    ],
                ])
                ->toArray();
        }

        parent::__construct([
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'fields' => Arr::flatten($fields),
        ]);
    }

    /**
     * Mount the type definition string.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function toGraphQLString(): string
    {
        $fields = [];
        if ($this instanceof HasFields) {
            $fields[] = collect($this->fields())
                ->filter(fn (Field $field) => $field instanceof DisplayableField && $field->canBeDisplayed())
                ->map(function (Field $field) {
                    return Str::of('"')
                        ->append($field->description())
                        ->append('"')
                        ->newLine()
                        ->append($field->__toString())
                        ->__toString();
                })
                ->toArray();
        }

        if ($this instanceof HasRelations) {
            $fields[] = Arr::map($this->relations(), fn (Relation $relation) => $relation->__toString());
        }

        if (blank($fields)) {
            throw new RuntimeException("There are no fields for the type {$this->getName()}.");
        }

        $fieldsString = implode(PHP_EOL, Arr::flatten($fields));

        $directives = '';
        if ($this instanceof HasDirectives) {
            $directives = $this->resolveDirectives($this->directives());
        }

        return "
            \"\"\"{$this->description()}\"\"\"
            type {$this->getName()} {$directives}{
                $fieldsString
            }
        ";
    }

    /**
     * The name displayed of type.
     *
     * @return string
     */
    public function getName(): string
    {
        return Str::of(class_basename($this))
            ->remove('Type')
            ->__toString();
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    abstract public function getDescription(): string;
}
