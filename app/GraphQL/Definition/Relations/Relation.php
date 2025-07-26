<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Relations;

use App\Concerns\GraphQL\ResolvesArguments;
use App\Concerns\GraphQL\ResolvesAttributes;
use App\Concerns\GraphQL\ResolvesDirectives;
use App\Contracts\GraphQL\HasFields;
use App\Enums\GraphQL\RelationType;
use App\GraphQL\Definition\Argument\Argument;
use App\GraphQL\Definition\Types\BaseType;
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

    /**
     * @param  class-string|null  $edgeType
     */
    public function __construct(
        protected Type $type,
        protected string $relationName,
        protected ?string $field = null,
        protected ?string $edgeType = null,
        protected ?bool $nullable = true,
    ) {}

    /**
     * Get the field as a string representation.
     */
    public function __toString(): string
    {
        $directives = $this->resolveDirectives(
            $this->relation()->getDirective([
                'relation' => $this->relationName,
                'edgeType' => class_exists($this->edgeType ?? '') ? (new $this->edgeType)->getName() : null,
            ])
        );

        return Str::of($this->field ?? $this->relationName)
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

        if ($type instanceof HasFields) {
            $arguments[] = $this->resolveFilterArguments($type->fields());
        }

        if ($type instanceof BaseType && $type instanceof HasFields && $this->type() instanceof ListOfType) {
            $arguments[] = $this->resolveSortArguments($type);
        }

        return Arr::flatten($arguments);
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
