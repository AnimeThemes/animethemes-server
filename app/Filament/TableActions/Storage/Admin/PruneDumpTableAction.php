<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\PruneDumpAction as PruneDump;
use App\Filament\TableActions\Storage\Base\PruneTableAction;
use App\Models\Admin\Dump;
use Illuminate\Support\Arr;

/**
 * Class PruneDumpTableAction.
 */
class PruneDumpTableAction extends PruneTableAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.dump.prune.name'));

        $this->authorize('forcedeleteany', Dump::class);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array  $fields
     * @return PruneDump
     */
    protected function storageAction(array $fields): PruneDump
    {
        $hours = Arr::get($fields, 'hours');

        return new PruneDump(intval($hours));
    }
}
