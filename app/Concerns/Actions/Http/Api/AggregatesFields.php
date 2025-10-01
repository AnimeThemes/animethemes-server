<?php

declare(strict_types=1);

namespace App\Concerns\Actions\Http\Api;

use App\Http\Api\Field\Aggregate\AggregateField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait AggregatesFields
{
    public function withAggregates(Builder $builder, Query $query, Schema $schema): Builder
    {
        collect($schema->fields())
            ->filter(fn (Field $field): bool => $field instanceof AggregateField && $field->shouldAggregate($query))
            ->each(fn (AggregateField $selectedAggregate): Builder => $selectedAggregate->with($query, $builder));

        return $builder;
    }

    public function loadAggregates(Model $model, Query $query, Schema $schema): Model
    {
        collect($schema->fields())
            ->filter(fn (Field $field): bool => $field instanceof AggregateField && $field->shouldAggregate($query))
            ->each(fn (AggregateField $selectedAggregate): Model => $selectedAggregate->load($query, $model));

        return $model;
    }
}
