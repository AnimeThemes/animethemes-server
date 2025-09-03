<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types;

use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Pivot\Base\NodeField;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Rebing\GraphQL\Support\Type as RebingType;

class EdgeType extends RebingType
{
    protected ?PivotType $pivotType;

    public function __construct(
        protected EloquentType $parentType,
        protected string $nodeType,
        ?string $pivotType = null,
    ) {
        $this->pivotType = class_exists($pivotType ?? '') ? new $pivotType : null;
    }

    /**
     * @return array<string,mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->getName(),
        ];
    }

    public function fields(): array
    {
        return collect($this->getPivotType()?->fieldClasses() ?? [])
            ->prepend(new NodeField($this->nodeType))
            ->flatMap(function (Field $field) {
                return [
                    $field->getName() => [
                        'type' => $field->baseType(),
                        'description' => $field->description(),
                        'alias' => $field->getColumn(),
                        'resolve' => $field->resolve(...),
                    ],
                ];
            })
            ->toArray();
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
     *
     * @return class-string<EloquentType> $nodeType
     */
    public function getNodeType(): string
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
