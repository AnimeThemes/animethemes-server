<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait SearchModels
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function search(Builder $builder, array $args): Builder
    {
        $search = Arr::get($args, 'search');

        if ($search !== null) {
            $model = $builder->getModel();

            /** @phpstan-ignore-next-line */
            $keys = $model::search($search)->keys();

            $builder->whereIn($model->getKeyName(), $keys);
        }

        return $builder;
    }
}
