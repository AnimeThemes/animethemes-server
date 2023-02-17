<?php

declare(strict_types=1);

namespace App\Concerns\Actions\Http\Api;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SelectsFields.
 */
trait SelectsFields
{
    /**
     * Selects fields for the query builder.
     *
     * @param  Builder  $builder
     * @param  Query  $query
     * @param  Schema  $schema
     * @return Builder
     */
    public function select(Builder $builder, Query $query, Schema $schema): Builder
    {
        $selectedFields = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SelectableField && $field->shouldSelect($query, $schema))
            ->map(fn (Field $field) => $field->getColumn());

        $builder->select($builder->qualifyColumns($selectedFields->all()));

        return $builder;
    }
}
