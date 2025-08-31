<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\PruneDumpAction as PruneDump;
use App\Filament\Actions\Storage\Base\PruneAction;
use App\Models\Admin\Dump;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class PruneDumpAction extends PruneAction
{
    public static function getDefaultName(): ?string
    {
        return 'prune-dump';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.dump.prune.name'));

        $this->visible(Gate::allows('deleteAny', Dump::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(?Model $record, array $data): PruneDump
    {
        $hours = Arr::get($data, 'hours');

        return new PruneDump(intval($hours));
    }
}
