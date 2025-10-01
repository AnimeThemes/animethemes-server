<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Relations;

use App\Concerns\Actions\GraphQL\FiltersModels;
use App\Concerns\GraphQL\ResolvesArguments;
use App\Enums\GraphQL\PaginationType;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Unions\BaseUnion;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\FirstArgument;
use App\GraphQL\Support\Argument\PageArgument;
use App\GraphQL\Support\Argument\SortArgument;
use App\GraphQL\Support\Argument\TrashedArgument;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;

abstract class Relation
{
    use FiltersModels;
    use ResolvesArguments;

    protected ?string $field = null;
    protected ?bool $nullable = true;
    protected Type $type;

    public function __construct(
        protected BaseType|BaseUnion $baseType,
        protected string $relationName,
    ) {
        $this->type = GraphQL::type($baseType->getName());
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
     * Resolve the arguments of the sub-query.
     *
     * @return Argument[]
     */
    protected function arguments(): array
    {
        $arguments = [];

        $type = $this->baseType;

        if ($this->paginationType() !== PaginationType::NONE && $type instanceof BaseType) {
            $arguments[] = $this->resolveFilterArguments($type->fieldClasses());
        }

        if ($this->paginationType() !== PaginationType::NONE) {
            $arguments[] = new FirstArgument(true);
            $arguments[] = new PageArgument();

            if ($type instanceof BaseType) {
                $arguments[] = new SortArgument($type);
            }
        }

        if ($type instanceof BaseType && in_array(new DeletedAtField(), $type->fieldClasses())) {
            $arguments[] = new TrashedArgument();
        }

        return Arr::flatten($arguments);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function args(): array
    {
        return collect($this->arguments())
            ->mapWithKeys(fn (Argument $argument): array => [
                $argument->name => [
                    'name' => $argument->name,
                    'type' => $argument->getType(),

                    ...(is_null($argument->getDefaultValue()) ? [] : ['defaultValue' => $argument->getDefaultValue()]),
                ],
            ])
            ->toArray();
    }

    public function getBaseType(): BaseType|BaseUnion
    {
        return $this->baseType;
    }

    /**
     * Resolve the relation.
     *
     * @param  array<string, mixed>  $args
     */
    public function resolve(Model $root, array $args): mixed
    {
        /** @var Collection $collection */
        $collection = $root->{$this->getRelationName()};

        $first = Arr::get($args, 'first');
        $page = Arr::get($args, 'page');

        return new LengthAwarePaginator(
            $collection->forPage($page, $first),
            $collection->count(),
            $first,
            $page
        );
    }

    abstract public function type(): Type;

    /**
     * The pagination type.
     */
    abstract public function paginationType(): PaginationType;
}
