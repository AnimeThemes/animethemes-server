<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use App\Concerns\Actions\Http\Api\ConstrainsEagerLoads;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShowAction.
 */
class ShowAction
{
    use ConstrainsEagerLoads;

    /**
     * Show model.
     *
     * @param  Model  $model
     * @param  Query  $query
     * @param  Schema  $schema
     * @return Model
     */
    public function show(Model $model, Query $query, Schema $schema): Model
    {
        // eager load relations with constraints
        $model->load($this->constrainEagerLoads($query, $schema));

        // Load aggregate relation values
        $this->loadAggregates($model, $query, $schema);

        return $model;
    }
}
