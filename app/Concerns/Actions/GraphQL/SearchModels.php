<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\Http\Api\Criteria\Search\Criteria;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
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

            $query = ElasticQuery::getForModel($model);

            $elasticBuilder = $query->build(new Criteria($search));

            $keys = $elasticBuilder->execute()->models()->modelKeys();

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
