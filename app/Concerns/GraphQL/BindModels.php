<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

use Illuminate\Database\Eloquent\Model;

trait BindModels
{
    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $model
     * @return ?TModel
     */
    protected function bind(string $model, string $column, string|int $value): ?Model
    {
        return $model::query()
            ->firstWhere($column, $value);
    }
}
