<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\PruneDumpAction as PruneDump;
use App\Filament\TableActions\Storage\Base\PruneTableAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Class PruneDumpTableAction.
 */
class PruneDumpTableAction extends PruneTableAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  Model  $model
     * @param  array  $fields
     * @return PruneDump
     */
    protected function storageAction(Model $model, array $fields): PruneDump
    {
        $hours = Arr::get($fields, 'hours');

        return new PruneDump(intval($hours));
    }
}
