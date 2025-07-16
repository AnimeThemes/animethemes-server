<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Relations;

use App\Concerns\GraphQL\ResolvesArguments;
use App\Concerns\GraphQL\ResolvesDirectives;
use App\Contracts\GraphQL\HasFields;
use App\Enums\GraphQL\RelationType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;
use Stringable;

/**
 * Class Relation.
 */
abstract class Relation implements Stringable
{
    use ResolvesArguments;
    use ResolvesDirectives;

    /**
     * @param  Type  $type
     * @param  string  $relationName
     * @param  string  $field
     * @param  bool|null  $nullable
     * @param  string|null  $edgeType
     */
    public function __construct(
        protected Type $type,
        protected string $relationName,
        protected ?string $field = null,
        protected ?bool $nullable = true,
        protected ?string $edgeType = null,
    ) {}

    /**
     * Get the field as a string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        $directives = $this->resolveDirectives(
            $this->relation()->getDirective([
                'relation' => $this->relationName,
                'edgeType' => $this->edgeType,
            ])
        );

        return Str::of($this->field ?? $this->relationName)
            ->append($this->getArguments())
            ->append(': ')
            ->append($this->type()->__toString())
            ->append(' ')
            ->append($directives)
            ->__toString();
    }

    /**
     * Resolve the arguments of the sub-query.
     *
     * @return string
     */
    protected function getArguments(): string
    {
        $arguments = [];

        if ($this->type instanceof HasFields) {
            $arguments[] = $this->resolveFilterArguments($this->type->fields());
        }

        return $this->buildArguments($arguments);
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    abstract protected function type(): Type;

    /**
     * The Relation type.
     *
     * @return RelationType
     */
    abstract protected function relation(): RelationType;
}
