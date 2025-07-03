<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\PruneDumpAction as PruneDump;
use App\Filament\Actions\Storage\Base\PruneAction;
use App\Models\Admin\Dump;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * Class PruneDumpAction.
 */
class PruneDumpAction extends PruneAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('prune-dump');

        $this->label(__('filament.actions.dump.prune.name'));

        $this->visible(Auth::user()->can('forcedeleteany', Dump::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Model|null  $model
     * @param  array  $fields
     * @return PruneDump
     */
    protected function storageAction(?Model $model, array $fields): PruneDump
    {
        $hours = Arr::get($fields, 'hours');

        return new PruneDump(intval($hours));
    }
}
