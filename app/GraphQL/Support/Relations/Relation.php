<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Relations;

use App\Concerns\GraphQL\ResolvesArguments;
use App\Concerns\GraphQL\ResolvesAttributes;
use App\Concerns\GraphQL\ResolvesDirectives;
use App\Enums\GraphQL\RelationType;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\EdgeType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Stringable;

abstract class Relation implements Stringable
{
    use ResolvesArguments;
    use ResolvesAttributes;
    use ResolvesDirectives;

    protected ?string $field = null;
    protected ?bool $nullable = true;
    protected ?EdgeType $edgeType = null;

    public function __construct(
        protected Type $type,
        protected string $relationName,
    ) {}

    /**
     * Rename the relation to something else.
     */
    public function renameTo(string $name): static
    {
        $this->field = $name;

        return $this;
    }

    /**
     * Mark the relation as not nullable.
     */
    public function notNullable(): static
    {
        $this->nullable = false;

        return $this;
    }

    /**
     * Get the name used to query through the type.
     * By default, the relation name is used.
     */
    public function getName(): string
    {
        return $this->field ?? $this->relationName;
    }

    /**
     * Get the field as a string representation.
     */
    public function __toString(): string
    {
        $directives = $this->resolveDirectives(
            $this->relation()->getDirective([
                'relation' => $this->relationName,
                'edgeType' => $this->edgeType ? $this->edgeType->getName() : null,
            ])
        );

        return Str::of($this->getName())
            ->append($this->buildArguments($this->arguments()))
            ->append(': ')
            ->append($this->type()->__toString())
            ->append(' ')
            ->append($directives)
            ->__toString();
    }

    /**
     * Resolve the arguments of the sub-query.
     *
     * @return Argument[]
     */
    protected function arguments(): array
    {
        $arguments = [];

        $type = $this->type;

        if ($type instanceof BaseType) {
            $arguments[] = $this->resolveFilterArguments($type->fields());
        }

        if ($type instanceof BaseType && $this->type() instanceof ListOfType) {
            $arguments[] = $this->resolveSortArguments($type);
        }

        return Arr::flatten($arguments);
    }

    /**
     * The type returned by the field.
     */
    public function getBaseType(): Type
    {
        return $this->type;
    }

    /**
     * The type returned by the field.
     */
    abstract protected function type(): Type;

    /**
     * The Relation type.
     */
    abstract protected function relation(): RelationType;
}
