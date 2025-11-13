<?php

declare(strict_types=1);

namespace App\Search;

use App\Contracts\Search\SearchDriver;
use App\Search\Drivers\CollectionDriver;
use App\Search\Drivers\ElasticsearchDriver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use RuntimeException;

class Search
{
    /**
     * @param  class-string<Model>|Model  $model
     */
    public static function search(Model|string $model, Criteria $criteria): SearchDriver
    {
        $model = $model instanceof Model ? $model : new $model;

        return match ($driver = Config::get('scout.driver')) {
            'elastic' => ElasticsearchDriver::search($model, $criteria),
            'collection' => CollectionDriver::search($model, $criteria),
            default => throw new RuntimeException("Unsupported {$driver} search driver configured."),
        };
    }
}
