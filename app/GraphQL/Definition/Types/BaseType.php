<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use App\Concerns\GraphQL\ResolvesDirectives;
use App\Contracts\GraphQL\HasDirectives;
use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Relations\Relation;
use GraphQL\Type\Definition\ObjectType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class BaseType.
 */
abstract class BaseType extends ObjectType
{
    use ResolvesDirectives;

    public function __construct()
    {
        $fields = [];
        if ($this instanceof HasFields) {
            $fields[] = collect($this->fields())
                ->mapWithKeys(fn (Field $field) => [
                    $field->getName() => [
                        'description' => $field->description(),
                        'type' => $field->getType(),
                        'resolve' => fn ($root) => $field->resolve($root),
                    ]
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
     */
    public function mount(): string
    {
        $fields = [];
        if ($this instanceof HasFields) {
            $fields[] = Arr::map($this->fields(), function (Field $field) {
                return Str::of('"')
                    ->append($field->description())
                    ->append('"')
                    ->append("\n")
                    ->append($field->toString())
                    ->__toString();
            });
        }

        if ($this instanceof HasRelations) {
            $fields[] = Arr::map($this->relations(), fn (Relation $relation) => $relation->toString());
        }

        $fieldsString = implode("\n", Arr::flatten($fields));

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
            ->replace('Type', '')
            ->__toString();
    }

    /**
     * The description of the type.
     *
     * @return string
     */
    abstract public function getDescription(): string;
}
