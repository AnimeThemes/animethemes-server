<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use App\Http\Api\Query\Query;
use Illuminate\Database\Eloquent\Model;

interface RenderableField
{
    /**
     * Determine if the field should be displayed to the user.
     */
    public function shouldRender(Query $query): bool;

    /**
     * Get the value to display to the user.
     */
    public function render(Model $model): mixed;
}
