<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Relations;

use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Concerns\Actions\GraphQL\SortsModels;
use App\Concerns\GraphQL\ResolvesArguments;
use App\Enums\GraphQL\PaginationType;
use App\Enums\GraphQL\RelationType;
use App\GraphQL\Definition\Types\Base\PaginatorType;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Unions\BaseUnion;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\EdgeType;
use GraphQL\Type\Definition\Type;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Facades\GraphQL;

abstract class Relation
{
    use FiltersModels;
    use ResolvesArguments;
    use SortsModels;

    protected ?string $field = null;
    protected ?bool $nullable = true;
    protected Type $type;
    protected ?EdgeType $edgeType = null;

    public function __construct(
        protected BaseType|BaseUnion $rebingType,
        protected string $relationName,
    ) {
        $this->type = GraphQL::type($rebingType->getName());
    }

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
     * Get the relation name in the model.
     */
    public function getRelationName(): string
    {
        return $this->relationName;
    }

    /**
     * Get the field as a string representation.
     */
    // public function __toString(): string
    // {
    //     $directives = $this->resolveDirectives(
    //         $this->relation()->getDirective([
    //             'relation' => $this->relationName,
    //             'edgeType' => $this->edgeType ? $this->edgeType->getName() : null,
    //         ])
    //     );

    //     return Str::of($this->getName())
    //         ->append($this->buildArguments($this->arguments()))
    //         ->append(': ')
    //         ->append($this->type()->__toString())
    //         ->append(' ')
    //         ->append($directives)
    //         ->__toString();
    // }

    /**
     * Resolve the arguments of the sub-query.
     *
     * @return Argument[]
     */
    protected function arguments(): array
    {
        $arguments = [];

        $type = $this->rebingType;

        $arguments[] = new Argument('first', Type::nonNull(Type::int()))->withDefaultValue(15);
        $arguments[] = new Argument('page', Type::int());

        if ($type instanceof BaseType) {
            $arguments[] = $this->resolveFilterArguments($type->fieldClasses());
        }

        // TODO: Fix
        if ($this->type() instanceof PaginatorType) {
            $arguments[] = $this->resolveSortArguments($this->rebingType);
        }

        return Arr::flatten($arguments);
    }

    /**
     * The args for the field.
     *
     * @return array<string, array<string, mixed>>
     */
    public function args(): array
    {
        return collect($this->arguments())
            ->mapWithKeys(fn (Argument $argument) => [
                $argument->name => [
                    'name' => $argument->name,
                    'type' => $argument->getType(),

                    ...(! is_null($argument->getDefaultValue()) ? ['defaultValue' => $argument->getDefaultValue()] : []),
                ],
            ])
            ->toArray();
    }

    /**
     * The type returned by the field.
     */
    public function getBaseType(): BaseType|BaseUnion
    {
        return $this->rebingType;
    }

    public function resolve(mixed $root, array $args = [])
    {
        return new LengthAwarePaginator($root->{$this->getRelationName()}, 15, 15);
    }

    /**
     * The type returned by the field.
     */
    abstract public function type(): Type;

    /**
     * The Relation type.
     */
    abstract protected function relation(): RelationType;

    /**
     * The pagination type.
     */
    abstract public function paginationType(): PaginationType;
}
