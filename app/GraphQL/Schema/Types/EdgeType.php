<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types;

use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Pivot\Base\NodeField;
use App\GraphQL\Schema\Fields\Relations\Relation;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class EdgeType extends BaseType
{
    public function __construct(
        protected EloquentType $parentType,
        protected EloquentType $nodeType,
        protected ?PivotType $pivotType = null,
    ) {}

    /**
     * @return array<string,mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->getName(),
        ];
    }

    public function relations(): array
    {
        return collect($this->getPivotType()?->relations() ?? [])
            ->filter(fn (Relation $relation): bool => $relation->isPivot())
            ->all();
    }

    public function fields(): array
    {
        $relations = collect($this->relations())
            ->flatMap(fn (Relation $relation): array => [
                $relation->getName() => [
                    'type' => $relation->type(),
                    'alias' => $relation->getRelationName(),
                    'resolve' => $relation->resolve(...),
                ],
            ]);

        return collect($this->getPivotType()?->fieldClasses() ?? [])
            ->reject(fn (Field $field): bool => $field instanceof Relation)
            ->prepend(new NodeField($this->nodeType))
            ->flatMap(fn (Field $field): array => [
                $field->getName() => [
                    'type' => $field->type(),
                    'description' => $field->description(),
                    'alias' => $field->getColumn(),
                    'args' => $field->args(),
                    'resolve' => $field->resolve(...),
                ],
            ])
            ->merge($relations)
            ->all();
    }

    /**
     * Get the name of the edge.
     * Template: {parentType}{nodeType}Edge.
     * Template: {nodeType}Edge.
     */
    public function getName(): string
    {
        return Str::of('')
            ->when($this->pivotType instanceof PivotType, fn (Stringable $string) => $string->append($this->parentType->getName()))
            ->append(class_basename($this->nodeType))
            ->remove('Type')
            ->append('Edge')
            ->__toString();
    }

    /**
     * Get the node type of the edge.
     */
    public function getNodeType(): EloquentType
    {
        return $this->nodeType;
    }

    /**
     * Get the pivot type of the edge.
     */
    public function getPivotType(): ?PivotType
    {
        return $this->pivotType;
    }
}
