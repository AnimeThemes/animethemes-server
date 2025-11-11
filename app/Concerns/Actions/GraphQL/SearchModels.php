<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\Search\Criteria;
use App\Search\Search;
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

            $keys = Search::search($model, new Criteria($search))
                ->keys();

            $builder->whereIn($model->getKeyName(), $keys);

            if (! Arr::has($args, 'sort')) {
                $builder->orderByRaw(sprintf(
                    'FIELD(%s, %s)',
                    $model->getKeyName(),
                    implode(',', $keys)
                ));
            }
        }

        return $builder;
    }
}
