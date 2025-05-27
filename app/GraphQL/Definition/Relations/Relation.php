<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Relations;

use App\Enums\GraphQL\RelationType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;

/**
 * Class Relation.
 */
abstract class Relation
{
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
    ) {
    }

    /**
     * Get the field as a string representation.
     *
     * @return string
     */
    public function toString(): string
    {
        return Str::of($this->field ?? $this->relationName)
            ->append(': ')
            ->append($this->type()->toString())
            ->append(' ')
            ->append($this->relation()->getDirective(['relation' => $this->relationName, 'edgeType' => $this->edgeType]))
            ->toString();
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
    protected function relation(): RelationType
    {
        return RelationType::BELONGS_TO_MANY;
    }
}
