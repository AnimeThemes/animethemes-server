<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\Actions\HasPivotActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use Filament\Tables\Actions\CreateAction as DefaultCreateAction;

/**
 * Class CreateAction.
 */
class CreateAction extends DefaultCreateAction
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

        $this->after(function ($livewire, $record) {
            if ($livewire instanceof BaseRelationManager) {
                $this->pivotActionLog('Create and Attach', $livewire, $record);
            }
        });
    }
}
