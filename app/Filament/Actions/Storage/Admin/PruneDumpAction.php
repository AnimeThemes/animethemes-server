<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\PruneDumpAction as PruneDump;
use App\Filament\Actions\Storage\Base\PruneAction;
use App\Models\Admin\Dump;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

/**
 * Class PruneDumpAction.
 */
class PruneDumpAction extends PruneAction
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'prune-dump';
    }

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.dump.prune.name'));

        $this->visible(Gate::allows('forceDeleteAny', Dump::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Model|null  $record
     * @param  array<string, mixed>  $data
     * @return PruneDump
     */
    protected function storageAction(?Model $record, array $data): PruneDump
    {
        $hours = Arr::get($data, 'hours');

        return new PruneDump(intval($hours));
    }
}
