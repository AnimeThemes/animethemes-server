<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\Actions\HasPivotActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use Filament\Tables\Actions\DetachAction as DefaultDetachAction;

/**
 * Class DetachAction.
 */
class DetachAction extends DefaultDetachAction
{
    use HasPivotActionLogs;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.detach'));

        $this->hidden(fn ($livewire) => !($livewire instanceof BaseRelationManager));

        $this->authorize('delete');

        $this->after(fn ($livewire, $record) => $this->pivotActionLog('Detach', $livewire, $record));
    }
}
