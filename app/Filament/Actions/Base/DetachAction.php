<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use Filament\Tables\Actions\DetachAction as DefaultDetachAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

        $this->hidden(fn ($livewire) => !($livewire instanceof BaseRelationManager && $livewire->getRelationship() instanceof BelongsToMany));

        $this->authorize('delete');

        $this->after(function ($livewire, $record) {
            $relationship = $livewire->getRelationship();

            if ($relationship instanceof BelongsToMany) {
                $this->pivotActionLog('Detach', $livewire, $record);
            }
        });
    }
}
