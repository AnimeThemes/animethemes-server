<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use App\Http\Api\Query\Query;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface RenderableField.
 */
interface RenderableField
{
    /**
     * Determine if the field should be displayed to the user.
     *
     * @param  Query  $query
     * @return bool
     */
    public function shouldRender(Query $query): bool;

    /**
     * Get the value to display to the user.
     *
     * @param  Model  $model
     * @return mixed
     */
    public function render(Model $model): mixed;
}
