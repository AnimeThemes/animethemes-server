<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Relations;

use App\Enums\GraphQL\PaginationType;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;

class HasOneRelation extends Relation
{
    public function type(): Type
    {
        if (! $this->nullable) {
            return Type::nonNull($this->type);
        }

        return $this->type;
    }

    /**
     * The pagination type if applicable.
     */
    public function paginationType(): PaginationType
    {
        return PaginationType::NONE;
    }

    /**
     * Resolve the relation.
     *
     * @param  array<string, mixed>  $args
     */
    public function resolve(Model $root, array $args): mixed
    {
        return $root->{$this->relationName};
    }
}
