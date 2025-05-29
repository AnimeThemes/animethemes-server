<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use App\Concerns\GraphQL\ResolvesDirectives;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Relations\Relation;
use GraphQL\Type\Definition\ObjectType;
use Illuminate\Support\Str;

/**
 * Class BaseType.
 */
abstract class BaseType extends ObjectType
{
    use ResolvesDirectives;

    public function __construct()
    {
        $fields = collect($this->fields())
            ->mapWithKeys(fn (Field $field) => [
                $field->getName() => [
                    'description' => $field->description(),
                    'type' => $field->getType(),
                    'resolve' => fn ($root) => $field->resolve($root),
                ]
            ])
            ->toArray();

        parent::__construct([
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'fields' => $fields,
        ]);
    }

    /**
     * Mount the type definition string.
     *
     * @return string
     */
    public function mount(): string
    {
        $fields = collect($this->fields())
            ->map(function (Field $field) {
                return Str::of('"')
                    ->append($field->description())
                    ->append('"')
                    ->append("\n                ")
                    ->append($field->toString());
            })
            ->implode("\n                ");

        $relations = collect($this->relations())
            ->map(fn (Relation $relation) => $relation->toString())
            ->implode("\n                ");

        $allFields = Str::of($fields)
            ->append("\n                ")
            ->append($relations)
            ->toString();

        $directives = filled($this->directives()) ? $this->resolveDirectives($this->directives()) : '';

        return "
            \"\"\"{$this->description()}\"\"\"
            type {$this->getName()} {$directives}{
                $allFields
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
            ->replace('Type', '')
            ->__toString();
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * The fields of the type.
     *
     * @return array
     */
    abstract public function fields(): array;

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [];
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
