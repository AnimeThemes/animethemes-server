<?php

declare(strict_types=1);

namespace App\Search;

use App\Contracts\Search\SearchBuilder;
use App\Search\Builders\CollectionBuilder;
use App\Search\Builders\ElasticSearchBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use RuntimeException;

class Search
{
    /**
     * @param  class-string<Model>|Model  $model
     */
    public static function search(Model|string $model, Criteria $criteria): SearchBuilder
    {
        $model = $model instanceof Model ? $model : new $model;

        return match (Config::get('scout.driver')) {
            'elastic' => ElasticSearchBuilder::search($model, $criteria),
            'collection' => CollectionBuilder::search($model, $criteria),
            default => throw new RuntimeException('Unsupported search driver configured.'),
        };
    }
}
