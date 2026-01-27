<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\GraphQL\Schema\Fields\Base\Aggregate\AggregateField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\BaseType;
use App\GraphQL\Schema\Unions\BaseUnion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait AggregatesFields
{
    public function withAggregates(Builder $builder, array $args, array $selection, BaseType|BaseUnion $type): Builder
    {
        if ($type instanceof BaseUnion) {
            return $builder;
        }

        collect($type->fieldClasses())
            ->filter(fn (Field $field): bool => $field instanceof AggregateField && $field->shouldAggregate($args, $selection, $type))
            ->each(fn (AggregateField $selectedAggregate): Builder => $selectedAggregate->with($builder));

        return $builder;
    }

    public function loadAggregates(Model $model, array $args, array $selection, BaseType|BaseUnion $type): Model
    {
        if ($type instanceof BaseUnion) {
            return $model;
        }

        collect($type->fieldClasses())
            ->filter(fn (Field $field): bool => $field instanceof AggregateField && $field->shouldAggregate($args, $selection, $type))
            ->each(fn (AggregateField $selectedAggregate): Model => $selectedAggregate->load($model));

        return $model;
    }
}
