<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use App\Http\Api\Query\Query;
use Illuminate\Database\Eloquent\Model;

interface RenderableField
{
    public function shouldRender(Query $query): bool;

    public function render(Model $model): mixed;
}
