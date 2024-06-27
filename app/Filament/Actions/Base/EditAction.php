<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\Actions\HasPivotActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use Filament\Tables\Actions\EditAction as DefaultEditAction;

/**
 * Class EditAction.
 */
class EditAction extends DefaultEditAction
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

        $this->label(__('filament.actions.base.edit'));

        $this->after(function ($livewire, $record) {
            if ($livewire instanceof BaseRelationManager) {
                $this->pivotActionLog('Update Attached', $livewire, $record);
            }
        });
    }
}
