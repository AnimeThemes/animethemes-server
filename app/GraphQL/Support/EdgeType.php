<?php

declare(strict_types=1);

namespace App\GraphQL\Support;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Base\NodeField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable as SupportStringable;
use Stringable;

final readonly class EdgeType implements Stringable
{
    protected ?PivotType $pivotType;

    /**
     * @param  class-string<EloquentType>  $nodeType
     * @param  class-string<PivotType>|null  $pivotType
     */
    public function __construct(
        protected EloquentType $parentType,
        protected string $nodeType,
        ?string $pivotType = null,
    ) {
        $this->pivotType = class_exists($pivotType ?? '') ? new $pivotType : null;
    }

    /**
     * Node and pivot fields.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        $fields = $this->pivotType instanceof PivotType
            ? $this->pivotType->fields()
            : [new CreatedAtField(), new UpdatedAtField()];

        return [
            new NodeField($this->nodeType),

            ...$fields,
        ];
    }

    /**
     * Get the edge type as a string representation.
     */
    public function __toString(): string
    {
        $fields = collect($this->fields())
            ->filter(fn (Field $field) => $field instanceof DisplayableField && $field->canBeDisplayed())
            ->map(
                fn (Field $field) => Str::of('"')
                    ->append($field->description())
                    ->append('"')
                    ->newLine()
                    ->append($field->__toString())
                    ->__toString()
            )
            ->implode(PHP_EOL);

        return sprintf(
            '"""%1$s to use in belongs to many relationships"""
            type %1$s {
                %2$s
            }',
            $this->getName(),
            $fields,
        );
    }

    /**
     * Get the name of the edge.
     * Template: {parentType}{nodeType}Edge.
     * Template: {nodeType}Edge.
     */
    public function getName(): string
    {
        return Str::of('')
            ->when($this->pivotType instanceof PivotType, fn (SupportStringable $string) => $string->append($this->parentType->getName()))
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
